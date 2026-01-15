# Lydia IA - Version 2.2.6 

<div align="center">
  <img src="assets/lydia-logo.jpg" alt="Lydia IA" width="200" style="border-radius: 50%;"></div>

Cette version finalise le design "Less is more" avec zéro élément superflu.

### Corrections finales (v2.2.6)
- ✅ **Zéro URL dans le texte** : Mistral AI ne peut plus inclure d'URLs dans ses réponses
- ✅ **Prompt ultra-strict** : Instructions explicites avec exemples BON/INTERDIT
- ✅ **Contexte nettoyé** : Les URLs sont retirées du contexte envoyé à l'API
- ✅ **Design final parfait** : Texte propre + liens bleus en dessous, c'est tout

### Design final (depuis v2.2.5)
- ✅ **Pas d'icônes** : Suppression de tous les pictogrammes (📚, 🛍️, 📄, 📰)
- ✅ **Pas de fond coloré** : Suppression du fond bleu ciel
- ✅ **Pas de titre "Sources"** : Les liens apparaissent directement
- ✅ **Sources sous le texte** : Affichage vertical optimal pour mobile

### Optimisations (depuis v2.2.4)
- ✅ **Timeout 60s** : Plus de problèmes de timeout
- ✅ **Contexte allégé** : 3 sources au lieu de 5
- ✅ **Contenu réduit** : 2000 caractères max par source
- ✅ **Réponses rapides** : max_tokens à 300 (moins de 10 secondes)

### Fonctionnalités complètes (depuis v2.2.0)
- ✅ **Indexation automatique** : Articles, pages, produits WooCommerce
- ✅ **Recherche intelligente** : Algorithme de scoring par pertinence
- ✅ **Interface d'administration** : Page d'indexation avec statistiques
- ✅ **Logs de debug** : Consultation des logs en temps réel
- ✅ **Auto-indexation** : Mise à jour automatique à chaque publication

## 📱 Résultat visuel final

**Ce que l'utilisateur voit :**

```
[Question de l'utilisateur]

[Réponse de Lydia - texte naturel et propre]

Jean-Christophe Gilbert
Lydia est maintenant en open source sur GitHub !
Le Diagnostic Leio sur le projet IA1
```

**Ce que l'utilisateur NE voit PLUS :**
- ❌ URLs dans le texte de réponse
- ❌ Icônes/pictogrammes (📚, 🛍️, 📄)
- ❌ Fond coloré bleu ciel
- ❌ Titre "Sources :"
- ❌ Bordures ou cadres autour des sources

**Simple. Propre. Efficace.**

## 📦 Installation / Mise à jour

### Méthode recommandée

1. **Connectez-vous** à l'admin WordPress
2. **Extensions → Lydia IA → Désactiver**
3. **Via FTP ou gestionnaire de fichiers** :
   - Allez dans `/wp-content/plugins/lydia-ia-plugin-main/`
   - Remplacez `lydia-ai-plugin.php` par le nouveau
4. **Réactivez** le plugin
5. **Vérifiez** la version dans Extensions (doit afficher 2.2.6)

### Première utilisation

Si vous installez Lydia pour la première fois :

1. **Installez le plugin** (méthode ci-dessus)
2. **Lydia IA → Configuration** :
   - Ajoutez votre clé API Mistral (obtenue sur console.mistral.ai)
   - Sélectionnez le modèle : mistral-small-latest (recommandé)
   - Décochez Wikipedia si vous voulez utiliser uniquement votre contenu
3. **Lydia IA → Indexation** :
   - Cliquez sur "🔄 Réindexer tout le contenu"
   - Attendez la fin (quelques secondes)
   - Vérifiez les statistiques (articles, pages, produits)
4. **Ajoutez le shortcode** sur une page : `[lydia_chat]`
5. **Testez !**

## ⚙️ Configuration recommandée

### Paramètres optimaux

**Clé API Mistral :** Obligatoire
- Obtenez-la sur https://console.mistral.ai
- Mistral offre des crédits gratuits pour tester
- Pay-as-you-go ensuite (~0,001€ à 0,003€ par question)

**Modèle :** mistral-small-latest
- Bon équilibre qualité/prix/vitesse
- Suffisant pour la plupart des sites

**Wikipedia :** Désactivé (recommandé)
- Lydia fonctionne très bien avec uniquement votre contenu
- Activez seulement si vous voulez enrichir avec des infos générales

### Indexation

**Automatique :** 
- Se déclenche à chaque publication/modification
- Rien à faire !

**Manuelle :** 
- Lydia IA → Indexation → Réindexer tout le contenu
- À faire après l'installation initiale
- Utile si vous modifiez beaucoup de contenu en masse

## 🎨 Personnalisation

### Modifier la hauteur du chat

Par défaut, la hauteur minimale est 350px. Pour la changer :

```
[lydia_chat height="500px"]
```

### Modifier le placeholder

```
[lydia_chat placeholder="Posez votre question ici..."]
```

### Exemple complet

```
[lydia_chat height="400px" placeholder="Comment puis-je vous aider ?"]
```

## 🔍 Debug et logs

### Consulter les logs

1. **Lydia IA → 🔍 Logs Debug**
2. Posez une question sur le site
3. Actualisez la page des logs
4. Examinez les messages

### Informations dans les logs

- Question reçue
- Recherche dans l'index (nombre de résultats)
- Contexte construit (taille)
- Envoi à Mistral (modèle, taille)
- Réponse Mistral OK/Erreur

### Effacer les logs

Bouton "🗑️ Effacer" dans la page des logs.

## 🧪 Tests après installation

### Test 1 : Vérifier l'indexation

1. **Lydia IA → Indexation**
2. Vérifiez que le nombre d'éléments indexés correspond à votre contenu
3. Si c'est 0, cliquez sur "Réindexer"

### Test 2 : Question simple

Posez : "Qui êtes-vous ?"
- ✅ Réponse en moins de 10 secondes
- ✅ Sources cliquables en dessous
- ✅ Pas d'URL dans le texte

### Test 3 : Question sur un produit (si WooCommerce)

Posez : "Quels produits vendez-vous ?"
- ✅ Lydia mentionne vos produits
- ✅ Sources pointent vers les pages produits
- ✅ Prix mentionnés (si dans la description)

### Test 4 : Question hors contenu

Posez : "Quelle est la capitale de la France ?"
- ✅ Lydia dit qu'elle ne trouve pas l'info dans le site
- ✅ Ou répond si Wikipedia est activé

## ⚡ Performances

### Temps de réponse

- **Question simple** : 3-8 secondes
- **Question complexe** : 8-15 secondes
- **Timeout** : 60 secondes max (puis erreur)

### Optimisations appliquées

- Contexte limité à 3 sources max (au lieu de 5)
- Contenu par source limité à 2000 caractères
- Réponses limitées à 300 tokens max
- Index stocké dans WordPress (pas de requête BDD lourde)

### Coûts Mistral AI

**Exemple concret :**
- 1000 visiteurs/mois
- 2 questions par visiteur
- = 2000 questions/mois
- = 6€ à 18€/mois selon le modèle

Vous ne payez **que** ce que vous consommez. Pas d'abonnement.

## 🛠️ Résolution de problèmes

### Erreur : "Clé API Mistral non configurée"

➡️ Allez dans Lydia IA → Configuration et ajoutez votre clé API

### Erreur : "L'API Mistral met trop de temps à répondre"

➡️ L'API est surchargée. Réessayez dans quelques instants.
➡️ Vérifiez votre connexion internet.

### Lydia ne répond pas correctement

➡️ Vérifiez l'indexation : Lydia IA → Indexation
➡️ Si 0 éléments indexés, cliquez sur "Réindexer"
➡️ Consultez les logs pour voir ce qui se passe

### Les sources ne s'affichent pas

➡️ Ouvrez F12 (console développeur)
➡️ Posez une question
➡️ Regardez les logs console : `Sources reçues: [...]`
➡️ Partagez le contenu des logs pour diagnostic

### Le chat ne s'affiche pas

➡️ Vérifiez que le shortcode `[lydia_chat]` est bien sur la page
➡️ Vérifiez qu'il n'y a pas d'erreur JavaScript (F12 → Console)
➡️ Désactivez les autres plugins temporairement pour tester

## 📊 Statistiques d'indexation

### Que contient l'index ?

Pour chaque élément indexé :
- **ID** : Identifiant WordPress
- **Type** : post, page, ou product
- **Titre** : Titre de l'article/page/produit
- **Contenu** : Extrait de 5000 caractères max (réduit à 2000 lors de l'envoi)
- **URL** : Lien vers la page
- **Date** : Date de publication

### Pour les produits WooCommerce

En plus du contenu standard :
- **Description courte**
- **Prix** (formaté avec €)
- **Catégories** (liste séparée par virgules)

### Exclusions

L'index n'inclut PAS :
- Les brouillons
- Les pages privées
- Les révisions
- Les contenus de moins de 30 caractères

## 🔐 Sécurité et données

### Souveraineté numérique

- ✅ **Plugin open source** : Code auditable sur GitHub
- ✅ **Données sur votre serveur** : L'index est stocké dans votre WordPress
- ✅ **API française** : Mistral AI (entreprise française, RGPD-compliant)
- ✅ **Pas de tracking** : Aucune donnée envoyée ailleurs que Mistral

### Ce qui est envoyé à Mistral AI

Pour chaque question :
- La question du visiteur
- Le contenu des 3 pages les plus pertinentes (max 2000 caractères chacune)

**Ce qui N'est PAS envoyé :**
- Informations personnelles des visiteurs
- Cookies ou IP
- Tout le contenu du site (seulement 3 pages pertinentes)

## 📝 Changelog complet

### Version 2.2.6 (2025-01-15) - FINALE
- ✅ Suppression totale des URLs dans le texte de réponse
- ✅ Prompt ultra-strict avec exemples BON/INTERDIT
- ✅ URLs retirées du contexte envoyé à Mistral AI
- ✅ Design minimaliste parfait

### Version 2.2.5 (2025-01-15)
- ✅ Suppression de tous les pictogrammes/icônes
- ✅ Suppression du fond bleu ciel
- ✅ Suppression du titre "Sources :"
- ✅ Design "Less is more"

### Version 2.2.4 (2025-01-15)
- ✅ Timeout augmenté de 30s à 60s
- ✅ Contexte optimisé (3 sources au lieu de 5)
- ✅ Contenu par source réduit (2000 caractères)
- ✅ max_tokens réduit à 300
- ✅ Messages d'erreur améliorés

### Version 2.2.3 (2025-01-15)
- ✅ Liens en bleu Google (#1A73E8)
- ✅ Prompt optimisé
- ✅ Logs de debug console

### Version 2.2.2 (2025-01-15)
- ✅ Affichage des sources sous chaque réponse
- ✅ Icônes distinctives par type
- ✅ Section sources avec design

### Version 2.2.1 (2025-01-15)
- ✅ Support complet WooCommerce
- ✅ Indexation des produits avec prix et catégories
- ✅ Statistiques incluent les produits

### Version 2.2.0 (2025-01-15)
- ✅ Système d'indexation complet
- ✅ Page d'administration
- ✅ Recherche intelligente par mots-clés
- ✅ Auto-indexation
- ✅ Wikipedia optionnel

## 🆘 Support

### Contact

**Jean-Christophe Gilbert**
- Email : jc@ia1.fr
- Téléphone : 06 40 75 53 92

**R2C SYSTEM SAS**
- Adresse : 38 rue de la Blauderie – 79000 Niort
- Téléphone : +33 5 79 70 58 24

### Ressources

- **Site officiel** : https://ia1.fr
- **Documentation Mistral AI** : https://docs.mistral.ai
- **Plugin open source** : Disponible sur demande

### Demande de fonctionnalité

Si vous souhaitez une nouvelle fonctionnalité, contactez-nous ! Nous sommes à l'écoute et le plugin évolue en fonction des besoins réels.

---

*Développé par IA1 • Propulsé par Mistral AI • Open Source & Souverain*

**Version 2.2.6 - Janvier 2025**
