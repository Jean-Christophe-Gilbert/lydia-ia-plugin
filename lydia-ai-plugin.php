<?php
/**
 * Plugin Name: Lydia IA (Debug Version)
 * Plugin URI: https://ia1.fr
 * Description: Version de diagnostic avec logs détaillés
 * Version: 2.1.1-debug
 * Author: IA1
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LYDIA_VERSION', '2.1.1-debug');
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
        
        add_shortcode('lydia_chat', array($this, 'chat_shortcode'));
        
        $this->mistral_api_key = get_option('lydia_mistral_api_key', '');
        
        lydia_log('Plugin Lydia initialisé');
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
        
        // Ajouter une page pour voir les logs
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
    
    public function register_settings() {
        register_setting('lydia_settings', 'lydia_mistral_api_key');
        register_setting('lydia_settings', 'lydia_model', array('default' => 'mistral-small-latest'));
        register_setting('lydia_settings', 'lydia_use_wikipedia', array('default' => true));
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>🤖 Configuration Lydia (Debug Version)</h1>
            
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
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="lydia_model">Modèle Mistral</label>
                        </th>
                        <td>
                            <select id="lydia_model" name="lydia_model">
                                <option value="mistral-small-latest" <?php selected(get_option('lydia_model'), 'mistral-small-latest'); ?>>
                                    mistral-small-latest
                                </option>
                                <option value="open-mistral-7b" <?php selected(get_option('lydia_model'), 'open-mistral-7b'); ?>>
                                    open-mistral-7b
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="lydia_use_wikipedia">
                                <input type="checkbox" 
                                       id="lydia_use_wikipedia" 
                                       name="lydia_use_wikipedia" 
                                       value="1"
                                       <?php checked(get_option('lydia_use_wikipedia', true)); ?>
                                />
                                Utiliser Wikipédia
                            </label>
                        </th>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    private function extract_keywords($query) {
        lydia_log('Extraction des mots-clés', array('query' => $query));
        
        $stop_words = array(
            'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'mais',
            'est', 'sont', 'a', 'ont', 'tu', 'me', 'te', 'se', 'nous', 'vous',
            'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi',
            'peux', 'peut', 'pouvez', 'parler', 'dire', 'connais', 'sais'
        );
        
        $query_lower = mb_strtolower($query, 'UTF-8');
        $query_clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query_lower);
        $words = preg_split('/\s+/', $query_clean, -1, PREG_SPLIT_NO_EMPTY);
        
        $keywords = array();
        foreach ($words as $word) {
            if (strlen($word) >= 3 && !in_array($word, $stop_words)) {
                $keywords[] = $word;
            }
        }
        
        lydia_log('Mots-clés extraits', array('keywords' => $keywords));
        
        return $keywords;
    }
    
    public function handle_chat() {
        lydia_log('=== NOUVELLE REQUÊTE CHAT ===');
        
        try {
            check_ajax_referer('lydia_chat_nonce', 'nonce');
            
            $query = sanitize_text_field($_POST['query']);
            lydia_log('Question reçue', array('query' => $query));
            
            if (empty($query)) {
                lydia_log('ERREUR: Question vide');
                wp_send_json_error(array('message' => 'Question vide'));
            }
            
            $all_sources = array();
            $context_parts = array();
            
            // Recherche locale
            lydia_log('Début recherche locale');
            $local_results = $this->search_local_content($query);
            
            if (!empty($local_results['context'])) {
                lydia_log('Résultats locaux trouvés', array('count' => count($local_results['sources'])));
                $context_parts[] = "📍 Contenu de " . $this->site_name . " :\n" . $local_results['context'];
                $all_sources = array_merge($all_sources, $local_results['sources']);
            } else {
                lydia_log('Aucun résultat local trouvé');
            }
            
            // Wikipedia
            if (get_option('lydia_use_wikipedia', true)) {
                lydia_log('Début recherche Wikipedia');
                $wikipedia_results = $this->search_wikipedia($query);
                
                if (!empty($wikipedia_results['context'])) {
                    lydia_log('Résultats Wikipedia trouvés');
                    $context_parts[] = "📚 Wikipédia :\n" . $wikipedia_results['context'];
                    $all_sources = array_merge($all_sources, $wikipedia_results['sources']);
                }
            }
            
            // Appel Mistral
            lydia_log('Début appel Mistral');
            
            $system_prompt = "Tu es Lydia, l'assistante IA du site « {$this->site_name} ».
Tu privilégies TOUJOURS le contenu local quand disponible.";
            
            $user_message = $query;
            if (!empty($context_parts)) {
                $user_message = implode("\n\n", $context_parts) . "\n\nQuestion : " . $query;
            }
            
            $response = $this->call_mistral($system_prompt, $user_message);
            
            if ($response['success']) {
                lydia_log('Réponse Mistral obtenue');
                wp_send_json_success(array(
                    'answer' => $response['answer'],
                    'sources' => $all_sources
                ));
            } else {
                lydia_log('ERREUR Mistral', array('error' => $response['error']));
                wp_send_json_error(array('message' => $response['error']));
            }
            
        } catch (Exception $e) {
            lydia_log('EXCEPTION CRITIQUE', array(
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ));
            
            wp_send_json_error(array('message' => 'Erreur serveur: ' . $e->getMessage()));
        }
    }
    
    private function search_local_content($query) {
        $results = array('context' => '', 'sources' => array());
        
        try {
            $keywords = $this->extract_keywords($query);
            $search_terms = !empty($keywords) ? implode(' ', $keywords) : $query;
            
            lydia_log('Recherche WordPress', array('terms' => $search_terms));
            
            $search_args = array(
                's' => $search_terms,
                'posts_per_page' => 5,
                'post_type' => array('post', 'page'),
                'post_status' => 'publish',
                'orderby' => 'relevance'
            );
            
            $search_query = new WP_Query($search_args);
            lydia_log('Résultats recherche', array('found' => $search_query->found_posts));
            
            if ($search_query->have_posts()) {
                $context_parts = array();
                
                while ($search_query->have_posts()) {
                    $search_query->the_post();
                    
                    $title = get_the_title();
                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 50);
                    $url = get_permalink();
                    $date = get_the_date('d/m/Y');
                    
                    $context_parts[] = "- [$date] $title : $excerpt";
                    
                    $results['sources'][] = array(
                        'title' => $title,
                        'url' => $url,
                        'source' => $this->site_name,
                        'type' => get_post_type(),
                        'date' => $date
                    );
                }
                
                wp_reset_postdata();
                $results['context'] = implode("\n", $context_parts);
            }
            
        } catch (Exception $e) {
            lydia_log('ERREUR recherche locale', array('error' => $e->getMessage()));
        }
        
        return $results;
    }
    
    private function search_wikipedia($query) {
        $results = array('context' => '', 'sources' => array());
        
        try {
            $keywords = $this->extract_keywords($query);
            $search_query = !empty($keywords) ? implode(' ', $keywords) : $query;
            
            lydia_log('Recherche Wikipedia', array('query' => $search_query));
            
            $api_url = 'https://fr.wikipedia.org/w/api.php';
            
            $search_params = array(
                'action' => 'opensearch',
                'search' => $search_query,
                'limit' => 2,
                'format' => 'json'
            );
            
            $search_url = $api_url . '?' . http_build_query($search_params);
            $search_response = wp_remote_get($search_url, array('timeout' => 10));
            
            if (is_wp_error($search_response)) {
                lydia_log('Erreur Wikipedia search', array('error' => $search_response->get_error_message()));
                return $results;
            }
            
            $search_data = json_decode(wp_remote_retrieve_body($search_response), true);
            
            if (empty($search_data[1])) {
                lydia_log('Aucun résultat Wikipedia');
                return $results;
            }
            
            lydia_log('Wikipedia résultats trouvés', array('count' => count($search_data[1])));
            
            // Récupération du contenu (simplifié pour éviter timeout)
            $context_parts = array();
            
            for ($i = 0; $i < min(1, count($search_data[1])); $i++) {
                $title = $search_data[1][$i];
                $url = $search_data[3][$i];
                
                $context_parts[] = "- $title";
                
                $results['sources'][] = array(
                    'title' => $title,
                    'url' => $url,
                    'source' => 'Wikipédia',
                    'type' => 'article'
                );
            }
            
            $results['context'] = implode("\n", $context_parts);
            
        } catch (Exception $e) {
            lydia_log('ERREUR Wikipedia', array('error' => $e->getMessage()));
        }
        
        return $results;
    }
    
    private function call_mistral($system_prompt, $user_message) {
        try {
            if (empty($this->mistral_api_key)) {
                return array('success' => false, 'error' => 'Clé API manquante');
            }
            
            $model = get_option('lydia_model', 'mistral-small-latest');
            
            $body = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'system', 'content' => $system_prompt),
                    array('role' => 'user', 'content' => $user_message)
                ),
                'temperature' => 0.7,
                'max_tokens' => 1000
            );
            
            lydia_log('Appel API Mistral', array('model' => $model));
            
            $response = wp_remote_post('https://api.mistral.ai/v1/chat/completions', array(
                'timeout' => 30,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->mistral_api_key
                ),
                'body' => json_encode($body)
            ));
            
            if (is_wp_error($response)) {
                lydia_log('Erreur HTTP Mistral', array('error' => $response->get_error_message()));
                return array('success' => false, 'error' => $response->get_error_message());
            }
            
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (empty($data['choices'][0]['message']['content'])) {
                lydia_log('Réponse Mistral invalide', array('data' => $data));
                return array('success' => false, 'error' => 'Réponse invalide');
            }
            
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
                .lydia-message-sources-v2 { margin-top: 12px; padding: 12px 16px; background: rgba(255,107,53,0.05); border-left: 2px solid #FFB39E; border-radius: 6px; font-size: 13px; color: #6B6B6B; }
                .lydia-message-sources-v2 a { color: #E85A2A; text-decoration: none; font-weight: 500; }
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
                            Bonjour ! Je connais le contenu de <?php echo esc_html($this->site_name); ?>. Posez-moi une question !
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
                if (sources && sources.length > 0) {
                    html += '<div class="lydia-message-sources-v2">📚 Sources : ';
                    sources.forEach((s, i) => {
                        if (i > 0) html += ', ';
                        html += (s.source === 'Wikipédia' ? '📖' : '📍') + ' <a href="' + escapeHtml(s.url) + '" target="_blank">' + escapeHtml(s.title) + (s.date ? ' (' + s.date + ')' : '') + '</a>';
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
                    
                    if (data.success) {
                        addMessage(data.data.answer, false, data.data.sources);
                    } else {
                        addMessage('Désolée, une erreur est survenue: ' + (data.data?.message || 'Erreur inconnue'), false, null);
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
