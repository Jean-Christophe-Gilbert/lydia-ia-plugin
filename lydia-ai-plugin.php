<?php
/**
 * Plugin Name: Lydia
 * Plugin URI: https://ia1.fr
 * Description: Assistante IA locale avec indexation complète, recherche intelligente et souveraine fabriquée en France à Niort
 * Version: 2.2.6
 * Author: IA1
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LYDIA_VERSION', '2.2.6');
define('LYDIA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LYDIA_LOG_FILE', WP_CONTENT_DIR . '/lydia-debug.log');

/**
 * Fonction de log personnalisée
 */
function lydia_log($message, $context = array()) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    
    if (!empty($context)) {
        $log_entry .= "Context: " . print_r($context, true) . "\n";
    }
    
    $log_entry .= "---\n";
    
    file_put_contents(LYDIA_LOG_FILE, $log_entry, FILE_APPEND);
}

class Lydia_WordPress {
    
    private $mistral_api_key;
    private $site_name;
    private $site_url;
    
    public function __construct() {
        $this->site_name = get_bloginfo('name');
        $this->site_url = get_site_url();
        
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_lydia_chat', array($this, 'handle_chat'));
        add_action('wp_ajax_nopriv_lydia_chat', array($this, 'handle_chat'));
        add_action('wp_ajax_lydia_reindex', array($this, 'ajax_reindex'));
        
        // Auto-indexation lors de la publication/mise à jour de contenus
        add_action('save_post', array($this, 'auto_reindex'), 10, 2);
        
        add_shortcode('lydia_chat', array($this, 'chat_shortcode'));
        
        $this->mistral_api_key = get_option('lydia_mistral_api_key', '');
        
        lydia_log('Plugin Lydia amélioré initialisé - Version 2.2.2 (avec affichage des sources)');
    }
    
    public function admin_menu() {
        add_menu_page(
            'Lydia IA',
            'Lydia IA',
            'manage_options',
            'lydia-settings',
            array($this, 'settings_page'),
            'dashicons-format-chat',
            30
        );
        
        // Page d'indexation
        add_submenu_page(
            'lydia-settings',
            'Indexation',
            '📊 Indexation',
            'manage_options',
            'lydia-indexation',
            array($this, 'indexation_page')
        );
        
        // Page de logs
        add_submenu_page(
            'lydia-settings',
            'Logs de Debug',
            '🔍 Logs Debug',
            'manage_options',
            'lydia-debug-logs',
            array($this, 'debug_logs_page')
        );
    }
    
    public function debug_logs_page() {
        ?>
        <div class="wrap">
            <h1>🔍 Logs de Debug Lydia</h1>
            
            <p>
                <a href="?page=lydia-debug-logs" class="button button-primary">🔄 Actualiser</a>
                <a href="?page=lydia-debug-logs&action=clear" class="button" onclick="return confirm('Effacer les logs ?')">🗑️ Effacer</a>
            </p>
            
            <?php
            if (isset($_GET['action']) && $_GET['action'] === 'clear') {
                if (file_exists(LYDIA_LOG_FILE)) {
                    unlink(LYDIA_LOG_FILE);
                    echo '<div class="notice notice-success"><p>✅ Logs effacés</p></div>';
                }
            }
            
            if (file_exists(LYDIA_LOG_FILE)) {
                $logs = file_get_contents(LYDIA_LOG_FILE);
                $lines = explode("\n", $logs);
                $recent = array_slice($lines, -200);
                $recent_logs = implode("\n", $recent);
                
                echo '<textarea readonly style="width:100%; height:500px; font-family:monospace; font-size:12px; padding:10px;">';
                echo esc_textarea($recent_logs);
                echo '</textarea>';
            } else {
                echo '<div class="notice notice-info"><p>Aucun log pour le moment</p></div>';
            }
            ?>
        </div>
        <?php
    }
    
    /**
     * Page d'indexation
     */
    public function indexation_page() {
        $index_stats = $this->get_index_stats();
        
        ?>
        <div class="wrap">
            <h1>📊 Indexation du contenu</h1>
            
            <div class="card" style="max-width: 800px;">
                <h2>Statistiques d'indexation</h2>
                
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td><strong>Articles indexés</strong></td>
                            <td><?php echo $index_stats['posts']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Pages indexées</strong></td>
                            <td><?php echo $index_stats['pages']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Produits indexés</strong></td>
                            <td><?php echo $index_stats['products']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo $index_stats['total']; ?> éléments</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Dernière indexation</strong></td>
                            <td><?php echo $index_stats['last_index'] ?: 'Jamais'; ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <p style="margin-top: 20px;">
                    <button id="lydia-reindex-btn" class="button button-primary button-large">
                        🔄 Réindexer tout le contenu
                    </button>
                    <span id="lydia-reindex-status" style="margin-left: 15px;"></span>
                </p>
                
                <div id="lydia-reindex-progress" style="display: none; margin-top: 20px;">
                    <div style="background: #f0f0f1; border-radius: 4px; height: 30px; overflow: hidden;">
                        <div id="lydia-progress-bar" style="background: #2271b1; height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <p id="lydia-progress-text" style="margin-top: 10px; color: #646970;"></p>
                </div>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>ℹ️ Comment fonctionne l'indexation ?</h2>
                <p>Lydia indexe automatiquement vos articles, pages et produits pour pouvoir répondre aux questions de vos visiteurs.</p>
                <ul>
                    <li>✅ L'indexation se fait automatiquement à chaque publication/mise à jour</li>
                    <li>✅ Vous pouvez forcer une réindexation complète avec le bouton ci-dessus</li>
                    <li>✅ Seul le contenu public est indexé (pas les brouillons)</li>
                    <li>✅ Le titre, le contenu et l'URL de chaque page sont stockés</li>
                    <li>✅ Pour les produits WooCommerce : description, prix et catégories sont inclus</li>
                </ul>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#lydia-reindex-btn').on('click', function() {
                const btn = $(this);
                const status = $('#lydia-reindex-status');
                const progress = $('#lydia-reindex-progress');
                const progressBar = $('#lydia-progress-bar');
                const progressText = $('#lydia-progress-text');
                
                btn.prop('disabled', true);
                status.html('<span style="color: #2271b1;">⏳ Indexation en cours...</span>');
                progress.show();
                progressBar.css('width', '0%');
                progressText.text('Démarrage...');
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'lydia_reindex',
                        nonce: '<?php echo wp_create_nonce('lydia_reindex'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            progressBar.css('width', '100%');
                            progressText.text('Terminé !');
                            status.html('<span style="color: #00a32a;">✅ ' + response.data.message + '</span>');
                            
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            status.html('<span style="color: #d63638;">❌ ' + response.data.message + '</span>');
                            btn.prop('disabled', false);
                            progress.hide();
                        }
                    },
                    error: function() {
                        status.html('<span style="color: #d63638;">❌ Erreur réseau</span>');
                        btn.prop('disabled', false);
                        progress.hide();
                    },
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total * 100;
                                progressBar.css('width', percentComplete + '%');
                                progressText.text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Obtenir les statistiques de l'index
     */
    private function get_index_stats() {
        $index = get_option('lydia_content_index', array());
        $last_index = get_option('lydia_last_index_time', '');
        
        $stats = array(
            'posts' => 0,
            'pages' => 0,
            'products' => 0,
            'total' => count($index),
            'last_index' => $last_index ? date('d/m/Y à H:i:s', strtotime($last_index)) : ''
        );
        
        foreach ($index as $item) {
            if ($item['type'] === 'post') {
                $stats['posts']++;
            } elseif ($item['type'] === 'page') {
                $stats['pages']++;
            } elseif ($item['type'] === 'product') {
                $stats['products']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * AJAX pour réindexer
     */
    public function ajax_reindex() {
        check_ajax_referer('lydia_reindex', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission refusée'));
        }
        
        $result = $this->reindex_all_content();
        
        if ($result['success']) {
            wp_send_json_success(array('message' => $result['message']));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
    
    /**
     * Réindexer tout le contenu
     */
    public function reindex_all_content() {
        lydia_log('Début de la réindexation complète');
        
        $index = array();
        
        // Récupérer tous les posts publiés (articles, pages, produits WooCommerce)
        $posts = get_posts(array(
            'post_type' => array('post', 'page', 'product'),
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        foreach ($posts as $post) {
            $content = wp_strip_all_tags($post->post_content);
            
            // Pour les produits WooCommerce, ajouter des infos supplémentaires
            if ($post->post_type === 'product' && class_exists('WC_Product')) {
                $product = wc_get_product($post->ID);
                if ($product) {
                    // Ajouter description courte, prix, catégories
                    $short_desc = $product->get_short_description();
                    $price = $product->get_price();
                    $categories = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'names'));
                    
                    $content .= "\n\nDescription : " . wp_strip_all_tags($short_desc);
                    if ($price) {
                        $content .= "\nPrix : " . $price . "€";
                    }
                    if (!empty($categories)) {
                        $content .= "\nCatégories : " . implode(', ', $categories);
                    }
                }
            }
            
            $content = preg_replace('/\s+/', ' ', $content);
            $content = trim($content);
            
            if (strlen($content) < 30) {
                continue; // Ignorer les contenus trop courts
            }
            
            $index[] = array(
                'id' => $post->ID,
                'type' => $post->post_type,
                'title' => $post->post_title,
                'content' => substr($content, 0, 5000), // Limiter à 5000 caractères
                'url' => get_permalink($post->ID),
                'date' => $post->post_date
            );
        }
        
        update_option('lydia_content_index', $index);
        update_option('lydia_last_index_time', current_time('mysql'));
        
        lydia_log('Réindexation terminée', array('total' => count($index)));
        
        return array(
            'success' => true,
            'message' => count($index) . ' éléments indexés avec succès'
        );
    }
    
    /**
     * Auto-réindexation lors de la sauvegarde d'un post
     */
    public function auto_reindex($post_id, $post) {
        // Ignorer les révisions et les brouillons
        if (wp_is_post_revision($post_id) || $post->post_status !== 'publish') {
            return;
        }
        
        // Ignorer les types de posts autres que post, page et product
        if (!in_array($post->post_type, array('post', 'page', 'product'))) {
            return;
        }
        
        lydia_log('Auto-réindexation déclenchée', array('post_id' => $post_id, 'title' => $post->post_title, 'type' => $post->post_type));
        
        // Mettre à jour l'index
        $this->update_single_post_in_index($post);
    }
    
    /**
     * Mettre à jour un seul post dans l'index
     */
    private function update_single_post_in_index($post) {
        $index = get_option('lydia_content_index', array());
        
        $content = wp_strip_all_tags($post->post_content);
        
        // Pour les produits WooCommerce, ajouter des infos supplémentaires
        if ($post->post_type === 'product' && class_exists('WC_Product')) {
            $product = wc_get_product($post->ID);
            if ($product) {
                // Ajouter description courte, prix, catégories
                $short_desc = $product->get_short_description();
                $price = $product->get_price();
                $categories = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'names'));
                
                $content .= "\n\nDescription : " . wp_strip_all_tags($short_desc);
                if ($price) {
                    $content .= "\nPrix : " . $price . "€";
                }
                if (!empty($categories)) {
                    $content .= "\nCatégories : " . implode(', ', $categories);
                }
            }
        }
        
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        $new_item = array(
            'id' => $post->ID,
            'type' => $post->post_type,
            'title' => $post->post_title,
            'content' => substr($content, 0, 5000),
            'url' => get_permalink($post->ID),
            'date' => $post->post_date
        );
        
        // Trouver et remplacer ou ajouter
        $found = false;
        foreach ($index as $key => $item) {
            if ($item['id'] === $post->ID) {
                $index[$key] = $new_item;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $index[] = $new_item;
        }
        
        update_option('lydia_content_index', $index);
    }
    
    public function register_settings() {
        register_setting('lydia_settings', 'lydia_mistral_api_key');
        register_setting('lydia_settings', 'lydia_model', array('default' => 'mistral-small-latest'));
        register_setting('lydia_settings', 'lydia_use_wikipedia', array('default' => false)); // Désactivé par défaut
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>🤖 Configuration Lydia</h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>✅ Paramètres sauvegardés</p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="options.php">
                <?php settings_fields('lydia_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="lydia_mistral_api_key">Clé API Mistral</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="lydia_mistral_api_key" 
                                   name="lydia_mistral_api_key" 
                                   value="<?php echo esc_attr(get_option('lydia_mistral_api_key')); ?>" 
                                   class="regular-text"
                            />
                            <p class="description">
                                Obtenez votre clé sur <a href="https://console.mistral.ai" target="_blank">console.mistral.ai</a>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="lydia_model">Modèle Mistral</label>
                        </th>
                        <td>
                            <select id="lydia_model" name="lydia_model">
                                <option value="mistral-small-latest" <?php selected(get_option('lydia_model'), 'mistral-small-latest'); ?>>
                                    mistral-small-latest (Recommandé)
                                </option>
                                <option value="open-mistral-7b" <?php selected(get_option('lydia_model'), 'open-mistral-7b'); ?>>
                                    open-mistral-7b
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Options</th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       id="lydia_use_wikipedia" 
                                       name="lydia_use_wikipedia" 
                                       value="1"
                                       <?php checked(get_option('lydia_use_wikipedia', false)); ?>
                                />
                                Utiliser Wikipédia pour enrichir les réponses
                            </label>
                            <p class="description">
                                Si coché, Lydia complétera ses réponses avec des informations de Wikipédia.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>📌 Comment utiliser Lydia</h2>
            <p>Utilisez le shortcode <code>[lydia_chat]</code> sur n'importe quelle page de votre site.</p>
            <p>Exemple : créez une page "Lydia" et ajoutez simplement le shortcode.</p>
        </div>
        <?php
    }
    
    /**
     * Rechercher du contenu pertinent dans l'index
     */
    private function search_relevant_content($query, $limit = 5) {
        $index = get_option('lydia_content_index', array());
        
        if (empty($index)) {
            lydia_log('Index vide - aucun contenu trouvé');
            return array();
        }
        
        lydia_log('Recherche dans l\'index', array('query' => $query, 'index_size' => count($index)));
        
        // Extraire les mots-clés de la requête
        $keywords = $this->extract_keywords($query);
        
        // Scorer chaque élément de l'index
        $scored_items = array();
        foreach ($index as $item) {
            $score = $this->calculate_relevance_score($item, $keywords);
            if ($score > 0) {
                $scored_items[] = array(
                    'item' => $item,
                    'score' => $score
                );
            }
        }
        
        // Trier par score décroissant
        usort($scored_items, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Retourner les N meilleurs résultats
        $results = array_slice($scored_items, 0, $limit);
        
        lydia_log('Résultats de recherche', array(
            'found' => count($results),
            'scores' => array_map(function($r) { return $r['score']; }, $results)
        ));
        
        return array_map(function($r) { return $r['item']; }, $results);
    }
    
    /**
     * Extraire les mots-clés d'une requête
     */
    private function extract_keywords($text) {
        // Convertir en minuscules
        $text = mb_strtolower($text, 'UTF-8');
        
        // Retirer la ponctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Diviser en mots
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Retirer les mots vides français communs
        $stopwords = array('le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'à', 'au', 'aux', 'et', 'ou', 'est', 'sont', 'qui', 'que', 'quoi', 'comment', 'pourquoi', 'où', 'quand', 'quel', 'quelle');
        $keywords = array_diff($words, $stopwords);
        
        return array_values($keywords);
    }
    
    /**
     * Calculer le score de pertinence d'un élément
     */
    private function calculate_relevance_score($item, $keywords) {
        $score = 0;
        
        $title_lower = mb_strtolower($item['title'], 'UTF-8');
        $content_lower = mb_strtolower($item['content'], 'UTF-8');
        
        foreach ($keywords as $keyword) {
            // Score plus élevé si le mot-clé est dans le titre
            if (strpos($title_lower, $keyword) !== false) {
                $score += 10;
            }
            
            // Score si le mot-clé est dans le contenu
            $count = substr_count($content_lower, $keyword);
            $score += $count * 2;
        }
        
        return $score;
    }
    
    /**
     * Construire le contexte à partir du contenu du site
     */
    private function build_context_from_content($query) {
        $relevant_items = $this->search_relevant_content($query, 3); // Réduit de 5 à 3 pour optimiser
        
        if (empty($relevant_items)) {
            return array(
                'context' => "Aucun contenu pertinent trouvé sur le site.",
                'sources' => array()
            );
        }
        
        $context = "Contenu du site " . $this->site_name . " :\n\n";
        $sources = array();
        
        foreach ($relevant_items as $item) {
            $context .= "=== " . $item['title'] . " ===\n"; // URL retirée du contexte
            // Limiter encore plus le contenu pour éviter les timeouts
            $short_content = substr($item['content'], 0, 2000);
            $context .= $short_content . "\n\n";
            
            // Ajouter à la liste des sources
            $sources[] = array(
                'title' => $item['title'],
                'url' => $item['url'],
                'type' => $item['type']
            );
        }
        
        return array(
            'context' => $context,
            'sources' => $sources
        );
    }
    
    /**
     * Gérer les requêtes de chat
     */
    public function handle_chat() {
        check_ajax_referer('lydia_chat_nonce', 'nonce');
        
        $query = sanitize_text_field($_POST['query'] ?? '');
        
        if (empty($query)) {
            wp_send_json_error(array('message' => 'Question vide'));
        }
        
        lydia_log('Question reçue', array('query' => $query));
        
        if (empty($this->mistral_api_key)) {
            lydia_log('ERREUR: Clé API manquante');
            wp_send_json_error(array('message' => 'Clé API Mistral non configurée'));
        }
        
        // Construire le contexte et récupérer les sources
        $context_data = $this->build_context_from_content($query);
        $context = $context_data['context'];
        $sources = $context_data['sources'];
        
        // Enrichir avec Wikipedia si activé
        $use_wikipedia = get_option('lydia_use_wikipedia', false);
        if ($use_wikipedia) {
            $wiki_content = $this->search_wikipedia($query);
            if (!empty($wiki_content)) {
                $context .= "\n\n=== Informations complémentaires (Wikipedia) ===\n" . $wiki_content;
            }
        }
        
        lydia_log('Contexte construit', array('length' => strlen($context), 'sources' => count($sources)));
        
        // Appeler Mistral AI
        $result = $this->call_mistral_ai($query, $context, $sources);
        
        if ($result['success']) {
            wp_send_json_success(array(
                'answer' => $result['answer'],
                'sources' => $sources
            ));
        } else {
            wp_send_json_error(array('message' => $result['error']));
        }
    }
    
    /**
     * Rechercher sur Wikipedia
     */
    private function search_wikipedia($query) {
        try {
            // Extraire le sujet principal
            $keywords = $this->extract_keywords($query);
            $search_term = implode(' ', array_slice($keywords, 0, 3));
            
            $url = 'https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=' . 
                   urlencode($search_term) . 
                   '&utf8=&format=json&srlimit=1';
            
            $response = wp_remote_get($url);
            
            if (is_wp_error($response)) {
                return '';
            }
            
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (empty($data['query']['search'])) {
                return '';
            }
            
            $page_title = $data['query']['search'][0]['title'];
            
            // Récupérer le contenu de la page
            $url = 'https://fr.wikipedia.org/w/api.php?action=query&titles=' . 
                   urlencode($page_title) . 
                   '&prop=extracts&exintro&explaintext&format=json';
            
            $response = wp_remote_get($url);
            
            if (is_wp_error($response)) {
                return '';
            }
            
            $data = json_decode(wp_remote_retrieve_body($response), true);
            $pages = $data['query']['pages'];
            $page = reset($pages);
            
            return $page['extract'] ?? '';
            
        } catch (Exception $e) {
            lydia_log('Erreur Wikipedia', array('error' => $e->getMessage()));
            return '';
        }
    }
    
    /**
     * Appeler l'API Mistral
     */
    private function call_mistral_ai($query, $context, $sources = array()) {
        try {
            $model = get_option('lydia_model', 'mistral-small-latest');
            
            $system_prompt = "Tu es Lydia, l'assistante IA du site " . $this->site_name . ". " .
                           "Tu réponds aux questions des visiteurs en te basant UNIQUEMENT sur le contenu du site fourni dans le contexte. " .
                           "Règles ABSOLUES :\n" .
                           "- Sois précise, concise et amicale\n" .
                           "- Cite le nom des pages/produits quand tu les mentionnes\n" .
                           "- N'ÉCRIS JAMAIS d'URL, de lien, d'adresse web, de [https://...], ou de (URL: ...) dans ta réponse\n" .
                           "- Les liens seront ajoutés automatiquement, n'en mets AUCUN dans ton texte\n" .
                           "- Si l'information n'est pas dans le contexte, dis-le clairement\n" .
                           "- N'invente JAMAIS d'information\n" .
                           "- Reste naturelle dans ta réponse\n\n" .
                           "EXEMPLE INTERDIT : 'Tu peux voir plus d'infos ici [https://example.com]'\n" .
                           "EXEMPLE BON : 'Tu peux voir plus d'infos sur la page Jean-Christophe Gilbert'";
            
            // Construire le message utilisateur SANS mentionner les URLs
            $user_message = "Contexte du site :\n" . $context . "\n\n" .
                          "Question du visiteur : " . $query . "\n\n" .
                          "IMPORTANT : Réponds sans JAMAIS inclure d'URL ou de lien dans ton texte. Mentionne juste les noms des pages.";
            
            lydia_log('Envoi à Mistral', array(
                'model' => $model,
                'query_length' => strlen($query),
                'context_length' => strlen($context),
                'sources_count' => count($sources)
            ));
            
            $body = array(
                'model' => $model,
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => $system_prompt
                    ),
                    array(
                        'role' => 'user',
                        'content' => $user_message
                    )
                ),
                'temperature' => 0.3,
                'max_tokens' => 300 // Réduit de 500 à 300 pour des réponses plus rapides
            );
            
            $response = wp_remote_post('https://api.mistral.ai/v1/chat/completions', array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->mistral_api_key,
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode($body),
                'timeout' => 60 // Augmenté de 30 à 60 secondes
            ));
            
            if (is_wp_error($response)) {
                $error_msg = $response->get_error_message();
                lydia_log('ERREUR Mistral WP', array('error' => $error_msg));
                
                // Message d'erreur plus clair pour l'utilisateur
                if (strpos($error_msg, 'timed out') !== false) {
                    return array('success' => false, 'error' => 'L\'API Mistral met trop de temps à répondre. Veuillez réessayer dans quelques instants.');
                }
                
                return array('success' => false, 'error' => 'Erreur de connexion à l\'API Mistral. Vérifiez votre clé API.');
            }
            
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (empty($data['choices'][0]['message']['content'])) {
                lydia_log('Réponse Mistral invalide', array('data' => $data));
                return array('success' => false, 'error' => 'Réponse invalide');
            }
            
            lydia_log('Réponse Mistral OK');
            
            return array(
                'success' => true,
                'answer' => $data['choices'][0]['message']['content']
            );
            
        } catch (Exception $e) {
            lydia_log('EXCEPTION Mistral', array('error' => $e->getMessage()));
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
    
    public function chat_shortcode($atts) {
        $atts = shortcode_atts(array(
            'placeholder' => 'Demander à Lydia',
            'height' => '350px'
        ), $atts);
        
        $widget_id = 'lydia-' . uniqid();
        
        wp_enqueue_style('google-fonts-quicksand', 'https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');
        
        ob_start();
        ?>
        <div id="<?php echo esc_attr($widget_id); ?>" class="lydia-chat-widget-v2">
            <style>
                .lydia-chat-widget-v2 { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; max-width: 720px; margin: 0 auto; }
                .lydia-chat-container-v2 { background: #FFF; border: 1px solid #E5E5E5; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
                .lydia-chat-header-v2 { padding: 24px; border-bottom: 1px solid #E5E5E5; }
                .lydia-chat-header-text-v2 h2 { font-family: 'Quicksand', sans-serif; font-size: 22px; font-weight: 700; margin: 0; color: #1A1A1A; }
                .lydia-chat-messages-v2 { padding: 24px; min-height: <?php echo esc_attr($atts['height']); ?>; max-height: 450px; overflow-y: auto; background: #FAFAF9; }
                .lydia-message-v2 { margin-bottom: 20px; }
                .lydia-message-user-v2 { display: flex; justify-content: flex-end; }
                .lydia-message-assistant-v2 { display: flex; justify-content: flex-start; }
                .lydia-message-bubble-v2 { max-width: 85%; padding: 16px 20px; border-radius: 16px; font-size: 15px; line-height: 1.6; }
                .lydia-message-user-v2 .lydia-message-bubble-v2 { background: #1A1A1A; color: white; border-bottom-right-radius: 4px; }
                .lydia-message-assistant-v2 .lydia-message-bubble-v2 { background: #FFF; color: #1A1A1A; border: 1px solid #E5E5E5; border-bottom-left-radius: 4px; }
                .lydia-message-assistant-v2 { display: block; } /* Pour que les sources soient sous le texte */
                .lydia-message-sources-v2 { margin-top: 12px; padding: 0; font-size: 13px; line-height: 1.8; }
                .lydia-message-sources-v2 a { color: #1A73E8; text-decoration: none; display: block; margin: 3px 0; }
                .lydia-message-sources-v2 a:hover { text-decoration: underline; }
                .lydia-chat-input-container-v2 { padding: 20px; border-top: 1px solid #E5E5E5; background: #FFF; display: flex; gap: 12px; }
                .lydia-chat-input-v2 { flex: 1; padding: 14px 20px; border: 1px solid #E5E5E5; border-radius: 24px; font-size: 15px; background: #FAFAF9; resize: none; outline: none; max-height: 120px; }
                .lydia-chat-input-v2:focus { border-color: #FF6B35; background: #FFF; }
                .lydia-loading-v2 { display: inline-flex; gap: 4px; padding: 16px 20px; }
                .lydia-loading-dot-v2 { width: 6px; height: 6px; border-radius: 50%; background: #9B9B9B; animation: lydiaLoadingDot 1.4s ease-in-out infinite; }
                .lydia-loading-dot-v2:nth-child(2) { animation-delay: 0.2s; }
                .lydia-loading-dot-v2:nth-child(3) { animation-delay: 0.4s; }
                @keyframes lydiaLoadingDot { 0%,80%,100% { opacity: 0.3; transform: scale(0.8); } 40% { opacity: 1; transform: scale(1); } }
            </style>
            
            <div class="lydia-chat-container-v2">
                <div class="lydia-chat-header-v2">
                    <div class="lydia-chat-header-text-v2">
                        <h2>Lydia</h2>
                    </div>
                </div>
                
                <div class="lydia-chat-messages-v2" id="<?php echo esc_attr($widget_id); ?>-messages">
                    <div class="lydia-message-v2 lydia-message-assistant-v2">
                        <div class="lydia-message-bubble-v2">
                            Bonjour ! Je suis Lydia, votre assistante IA. Je connais le contenu de <?php echo esc_html($this->site_name); ?> et je peux vous aider à trouver des informations. Posez-moi une question !
                        </div>
                    </div>
                </div>
                
                <div class="lydia-chat-input-container-v2">
                    <textarea id="<?php echo esc_attr($widget_id); ?>-input" class="lydia-chat-input-v2" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" rows="1"></textarea>
                </div>
            </div>
        </div>
        
        <script>
        (function() {
            const widget = document.getElementById('<?php echo esc_js($widget_id); ?>');
            const messages = widget.querySelector('.lydia-chat-messages-v2');
            const input = widget.querySelector('.lydia-chat-input-v2');
            
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
            
            function addMessage(text, isUser, sources) {
                const div = document.createElement('div');
                div.className = 'lydia-message-v2 ' + (isUser ? 'lydia-message-user-v2' : 'lydia-message-assistant-v2');
                
                let html = '<div class="lydia-message-bubble-v2">' + escapeHtml(text) + '</div>';
                
                // Ajouter les sources si présentes et si c'est une réponse de l'assistant
                if (!isUser && sources && sources.length > 0) {
                    html += '<div class="lydia-message-sources-v2">';
                    sources.forEach((source) => {
                        html += '<a href="' + escapeHtml(source.url) + '" target="_blank">' + escapeHtml(source.title) + '</a>';
                    });
                    html += '</div>';
                }
                
                div.innerHTML = html;
                messages.appendChild(div);
                messages.scrollTop = messages.scrollHeight;
            }
            
            function addLoading() {
                const div = document.createElement('div');
                div.className = 'lydia-message-v2 lydia-message-assistant-v2';
                div.id = '<?php echo esc_js($widget_id); ?>-loading';
                div.innerHTML = '<div class="lydia-loading-v2"><div class="lydia-loading-dot-v2"></div><div class="lydia-loading-dot-v2"></div><div class="lydia-loading-dot-v2"></div></div>';
                messages.appendChild(div);
                messages.scrollTop = messages.scrollHeight;
            }
            
            function removeLoading() {
                const loading = document.getElementById('<?php echo esc_js($widget_id); ?>-loading');
                if (loading) loading.remove();
            }
            
            async function sendMessage() {
                const query = input.value.trim();
                if (!query) return;
                addMessage(query, true, null);
                input.value = '';
                input.style.height = 'auto';
                addLoading();
                
                try {
                    const formData = new FormData();
                    formData.append('action', 'lydia_chat');
                    formData.append('nonce', '<?php echo wp_create_nonce('lydia_chat_nonce'); ?>');
                    formData.append('query', query);
                    
                    const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    removeLoading();
                    
                    console.log('Réponse Lydia:', data); // DEBUG
                    
                    if (data.success) {
                        console.log('Sources reçues:', data.data.sources); // DEBUG
                        addMessage(data.data.answer, false, data.data.sources || []);
                    } else {
                        addMessage('Désolée, je n\'ai pas pu traiter votre question. ' + (data.data?.message || ''), false, null);
                    }
                } catch (error) {
                    removeLoading();
                    addMessage('Erreur de connexion', false, null);
                }
                
                input.focus();
            }
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

new Lydia_WordPress();
