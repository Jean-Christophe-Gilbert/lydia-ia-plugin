# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

## [2.1.0] - 2025-01-11

### Ajouté
- Plugin vraiment générique - s'adapte automatiquement au nom du site
- Extraction intelligente des mots-clés pour une meilleure recherche
- Option pour activer/désactiver Wikipedia dans les réglages
- Affichage des dates dans les sources citées
- Amélioration du prompt système pour Mistral

### Modifié
- Recherche toujours prioritaire dans le contenu local avant Wikipedia
- Interface utilisateur plus claire et moderne
- Optimisation des performances de recherche

### Corrigé
- Problèmes d'encodage avec les caractères spéciaux
- Gestion améliorée des erreurs API
- Responsive design sur petits écrans

## [2.0.0] - 2024-12-15

### Ajouté
- Version initiale publique
- Intégration Mistral AI
- Recherche dans le contenu WordPress
- Support Wikipedia optionnel
- Design moderne et responsive
- Shortcode `[lydia_chat]`
- Page d'administration WordPress
- Extraction automatique des mots-clés
- Citations des sources avec liens cliquables

### Sécurité
- Validation et sanitisation des entrées utilisateur
- Protection contre les injections SQL
- Nonces pour les requêtes AJAX

## [1.0.0-beta] - 2024-11-01

### Ajouté
- Prototype initial
- Intégration de base avec Mistral AI
- Interface de chat simple

---

## Types de changements

- **Ajouté** : pour les nouvelles fonctionnalités
- **Modifié** : pour les changements dans les fonctionnalités existantes
- **Déprécié** : pour les fonctionnalités qui seront bientôt supprimées
- **Supprimé** : pour les fonctionnalités supprimées
- **Corrigé** : pour les corrections de bugs
- **Sécurité** : en cas de vulnérabilités

[2.1.0]: https://github.com/votre-organisation/lydia-ia-plugin/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/votre-organisation/lydia-ia-plugin/compare/v1.0.0-beta...v2.0.0
[1.0.0-beta]: https://github.com/votre-organisation/lydia-ia-plugin/releases/tag/v1.0.0-beta
