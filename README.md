# Lydia - Version 2.2.8

[![Lydia IA](assets/lydia-logo.jpg)](assets/lydia-logo.jpg)

**Assistante IA locale pour WordPress - Souveraine, Open Source et Française**

Cette version optimise les performances pour des réponses **ultra-rapides** (moins de 10 secondes).

---

## ⚡ Nouveautés version 2.2.8

### Optimisations de performance

* ✅ **Réponses 30% plus rapides** : Contexte et tokens optimisés
* ✅ **2 sources au lieu de 3** : Focus sur la pertinence maximale
* ✅ **800 caractères par source** : Contexte allégé pour vitesse optimale
* ✅ **Timeout 30s** : Détection rapide des problèmes réseau
* ✅ **Messages d'erreur améliorés** : Plus clairs pour l'utilisateur

### Architecture technique

* Recherche intelligente par scoring de pertinence
* Indexation automatique (articles, pages, produits WooCommerce)
* API Mistral AI (française, RGPD-compliant)
* Interface moderne et épurée
* Système de logs intégré

---

## 📦 Installation rapide

### Prérequis

* WordPress 5.8+
* PHP 7.4+
* Clé API Mistral (gratuite pour tester sur [console.mistral.ai](https://console.mistral.ai))

### Installation

1. **Téléchargez** la [dernière version](https://github.com/Jean-Christophe-Gilbert/lydia-ia-plugin/releases)
2. **Uploadez** le dossier dans `/wp-content/plugins/`
3. **Activez** le plugin dans WordPress
4. **Configurez** : Lydia IA → Configuration
   * Ajoutez votre clé API Mistral
   * Sélectionnez `mistral-small-latest` (recommandé)
5. **Indexez** : Lydia IA → Indexation → "Réindexer tout le contenu"
6. **Ajoutez** le shortcode `[lydia_chat]` sur une page

✅ **C'est prêt !**

---

## 🎯 Fonctionnalités complètes

### 🔍 Recherche intelligente
Lydia indexe automatiquement vos articles et pages pour répondre précisément aux questions de vos visiteurs.

### 💬 Conversation naturelle
Grâce à Mistral AI, Lydia comprend le langage naturel et mémorise le contexte de la conversation.

### 🛒 Support WooCommerce
Indexation automatique des produits avec prix, catégories et descriptions.

### 📊 Interface d'administration
Page d'indexation avec statistiques en temps réel et réindexation en un clic.

### 🔍 Logs de debug
Consultation des logs en temps réel pour diagnostiquer rapidement tout problème.

### 🎨 Design moderne
Interface épurée, responsive, qui s'adapte à votre charte graphique.

---

## ⚙️ Configuration recommandée

### Paramètres Mistral AI

| Paramètre | Valeur recommandée | Pourquoi |
|-----------|-------------------|----------|
| **Modèle** | `mistral-small-latest` | Meilleur rapport qualité/prix/vitesse |
| **Temperature** | `0.5` | Réponses rapides et cohérentes |
| **Max tokens** | `250` | Réponses concises (< 10s) |
| **Timeout** | `30s` | Détection rapide des problèmes |

### Coûts (Pay-as-you-go)

**Exemple concret** :
* 1000 visiteurs/mois × 2 questions = **2000 questions**
* Coût : **6 à 18€/mois** selon le modèle

💡 Vous ne payez **que** ce que vous consommez. Pas d'abonnement.

---

## 🚀 Utilisation

### Shortcode de base

```
[lydia_chat]
```

### Avec personnalisation

```
[lydia_chat height="500px" placeholder="Posez votre question ici..."]
```

### Résultat visuel

```
[Question de l'utilisateur]

[Réponse de Lydia - texte naturel et propre]

Jean-Christophe Gilbert
Lydia est maintenant en open source sur GitHub !
Le Diagnostic Leio sur le projet IA1
```

**Simple. Propre. Efficace.**

---

## 📊 Performance

### Benchmarks version 2.2.8

| Type de question | Temps moyen | Temps max |
|-----------------|-------------|-----------|
| Question simple | 3-6s | 10s |
| Question complexe | 6-10s | 15s |
| Timeout | - | 30s |

### Optimisations appliquées

* Contexte limité à 2 sources (au lieu de 3)
* 800 caractères par source (au lieu de 2000)
* 250 tokens max (au lieu de 300)
* Index stocké dans WordPress (pas de requête BDD lourde)

---

## 🔐 Souveraineté et sécurité

### ✅ Données sous contrôle

* Plugin **100% open source** - Code auditable
* Index stocké **sur votre serveur** WordPress
* API **Mistral AI** (entreprise française)
* **RGPD-compliant** - Pas de tracking
* Aucune dépendance américaine

### 📤 Ce qui est envoyé à Mistral

Pour chaque question :
* La question du visiteur
* Le contenu des 2 pages les plus pertinentes (800 caractères max chacune)

**Ce qui N'est PAS envoyé** :
* Informations personnelles
* Cookies ou adresses IP
* L'intégralité du contenu du site

---

## 🛠️ Développement et contribution

### Structure du projet

```
lydia-ia-plugin/
├── lydia-ai-plugin.php    # Fichier principal
├── assets/                # Images et ressources
├── README.md             # Ce fichier
├── CHANGELOG.md          # Historique des versions
└── LICENSE               # Licence MIT
```

### Contribuer

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

Voir [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.

---

## 📝 Changelog

Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique complet des versions.

### Version 2.2.8 (15 janvier 2025)
* 🚀 Optimisations de performance (réponses 30% plus rapides)
* 🚀 Réduction du contexte à 2 sources (au lieu de 3)
* 🚀 Contenu par source réduit à 800 caractères
* 🚀 Timeout optimisé à 30 secondes
* 🔧 Messages d'erreur améliorés

---

## 🆘 Support et contact

### Documentation

* [Guide d'installation](https://github.com/Jean-Christophe-Gilbert/lydia-ia-plugin#-installation-rapide)
* [Guide de déploiement](GUIDE-DEPLOIEMENT.md)
* [FAQ](https://github.com/Jean-Christophe-Gilbert/lydia-ia-plugin/wiki)

### Besoin d'aide ?

**Jean-Christophe Gilbert**
* Email : [jc@ia1.fr](mailto:jc@ia1.fr)
* Téléphone : 06 40 75 53 92

**R2C SYSTEM SAS**
* Adresse : 38 rue de la Blauderie – 79000 Niort
* Téléphone : +33 5 79 70 58 24
* Site : [ia1.fr](https://ia1.fr)

### Signaler un bug

Ouvrez une [issue sur GitHub](https://github.com/Jean-Christophe-Gilbert/lydia-ia-plugin/issues) en décrivant :
* Le problème rencontré
* Les étapes pour le reproduire
* Votre environnement (WordPress, PHP, navigateur)

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🌟 Remerciements

Lydia est développé par **IA1** (R2C SYSTEM SAS) à Niort, en France.

Propulsé par **Mistral AI**, l'intelligence artificielle française et souveraine.

---

**Version 2.2.8** - Janvier 2025

*Développé par IA1 • Propulsé par Mistral AI • Open Source & Souverain*
