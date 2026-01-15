# Changelog Lydia

Toutes les modifications notables du plugin Lydia seront documentées dans ce fichier.

## [2.2.8] - 2025-01-15

### 🚀 Optimisations de performance
- **Réduction du nombre de sources** : Passage de 3 à 2 sources pour une réponse plus rapide
- **Contenu par source réduit** : 800 caractères au lieu de 2000 pour optimiser la vitesse
- **Réponses plus courtes** : max_tokens réduit à 250 (au lieu de 300)
- **Temperature ajustée** : Augmentée à 0.5 pour une génération plus rapide
- **Timeout optimisé** : Réduit à 30 secondes pour détecter plus vite les problèmes
- **Messages d'erreur améliorés** : Message plus clair en cas de timeout

### 🎯 Objectif
Cette version vise des réponses en **moins de 10 secondes** pour une meilleure expérience utilisateur.

---

## [2.2.7] - 2025-01-15

### 🔧 Corrections et ajustements
- Ajustements mineurs de performance
- Tests de stabilité

---

## [2.2.6] - 2025-01-15

### ✨ Design "Less is more" finalisé
- **Zéro URL dans le texte** : Mistral AI ne peut plus inclure d'URLs dans ses réponses
- **Prompt ultra-strict** : Instructions explicites avec exemples BON/INTERDIT
- **Contexte nettoyé** : Les URLs sont retirées du contexte envoyé à l'API
- **Design final parfait** : Texte propre + liens bleus en dessous

### 🎨 Améliorations visuelles
- **Suppression de tous les pictogrammes/icônes** (📚, 🛍️, 📄, 📰)
- **Suppression du fond bleu ciel**
- **Suppression du titre "Sources :"**
- **Sources sous le texte** : Affichage vertical optimal pour mobile

---

## [2.2.5] - 2025-01-15

### 🎨 Design minimaliste
- Suppression de tous les éléments visuels superflus
- Design épuré "Less is more"

---

## [2.2.4] - 2025-01-15

### ⚡ Optimisations
- **Timeout augmenté** : De 30s à 60s
- **Contexte optimisé** : 3 sources au lieu de 5
- **Contenu par source** : Réduit à 2000 caractères
- **max_tokens** : Réduit à 300
- **Messages d'erreur** : Améliorés pour plus de clarté

---

## [2.2.3] - 2025-01-15

### 🎨 Améliorations visuelles
- Liens en bleu Google (#1A73E8)
- Prompt optimisé
- Logs de debug console

---

## [2.2.2] - 2025-01-15

### ✨ Affichage des sources
- Affichage des sources sous chaque réponse
- Icônes distinctives par type de contenu
- Section sources avec design soigné

---

## [2.2.1] - 2025-01-15

### 🛒 Support WooCommerce
- Support complet WooCommerce
- Indexation des produits avec prix et catégories
- Statistiques incluant les produits

---

## [2.2.0] - 2025-01-15

### 🎉 Version majeure - Système d'indexation complet
- **Système d'indexation** : Articles, pages, produits indexés automatiquement
- **Page d'administration** : Interface complète pour gérer l'indexation
- **Recherche intelligente** : Algorithme de scoring par pertinence
- **Auto-indexation** : Mise à jour automatique lors de la publication
- **Wikipedia optionnel** : Peut être désactivé pour utiliser uniquement le contenu local
- **Logs de debug** : Système de logs pour diagnostiquer les problèmes

---

## Versions antérieures

Les versions antérieures à 2.2.0 n'étaient pas versionnées de manière systématique.

---

## Légende

- 🎉 Nouvelle fonctionnalité majeure
- ✨ Nouvelle fonctionnalité
- 🚀 Optimisation de performance
- 🔧 Correction de bug
- 🎨 Amélioration visuelle/UX
- 🛒 WooCommerce
- 📝 Documentation
