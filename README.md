<div align="center">
  <img src="assets/lydia-logo.jpg" alt="Lydia IA" width="200" style="border-radius: 50%;">
  
  # Lydia IA
  
  **Un chatbot IA souverain qui connaît votre site WordPress**
  
  [![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
  [![WordPress Plugin](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org)
  [![Mistral AI](https://img.shields.io/badge/Powered%20by-Mistral%20AI-orange.svg)](https://mistral.ai)
  [![Made in France](https://img.shields.io/badge/Made%20in-France%20🇫🇷-blue.svg)](https://ia1.fr)
</div>

---

Lydia est un plugin WordPress open source qui intègre une intelligence artificielle locale sur votre site. Propulsé par Mistral AI (entreprise française), Lydia indexe automatiquement votre contenu et aide vos visiteurs à naviguer sur votre site.

## ✨ Fonctionnalités

- 🇫🇷 **IA souveraine** : Propulsé par Mistral AI, entreprise française respectant le RGPD
- 🔍 **Connaissance de votre site** : Indexation automatique de vos articles et pages
- 📖 **Wikipédia optionnel** : Enrichissement avec les connaissances générales
- 💰 **Pay-as-you-go** : Vous ne payez que ce que vous consommez (~0.001€ à 0.003€ par question)
- 🎨 **Design moderne** : Interface élégante et responsive
- 🔒 **Données sécurisées** : Vos données restent sur votre serveur
- 🌐 **100% open source** : Code transparent et auditable

## 📋 Prérequis

- WordPress 5.8 ou supérieur
- PHP 7.4 ou supérieur
- Clé API Mistral AI (gratuite pour tester)

## 🚀 Installation

### Installation depuis GitHub

1. Téléchargez la dernière version depuis les [Releases](https://github.com/votre-organisation/lydia-ia-plugin/releases)
2. Dans WordPress, allez dans **Extensions → Ajouter → Téléverser une extension**
3. Sélectionnez le fichier ZIP téléchargé
4. Activez le plugin
5. Allez dans **Réglages → Lydia IA**
6. Ajoutez votre clé API Mistral (obtenir sur [console.mistral.ai](https://console.mistral.ai))
7. Ajoutez le shortcode `[lydia_chat]` sur une page

### Installation depuis WordPress.org

*(En attente de publication sur le dépôt officiel)*

```bash
# Depuis l'admin WordPress
Extensions → Ajouter → Rechercher "Lydia IA"
```

## 🔧 Configuration

### Obtenir une clé API Mistral

1. Créez un compte sur [console.mistral.ai](https://console.mistral.ai)
2. Créez une nouvelle clé API
3. Copiez la clé dans les réglages de Lydia

Mistral AI offre des crédits gratuits pour tester. Ensuite, le coût est d'environ **0.001€ à 0.003€ par question**.

### Utilisation du shortcode

Ajoutez simplement le shortcode sur n'importe quelle page :

```
[lydia_chat]
```

Options disponibles :

```
[lydia_chat height="500px" placeholder="Posez votre question..."]
```

## 💡 Exemples d'utilisation

Lydia peut aider vos visiteurs à :

- 🔍 Trouver rapidement un article ou une page
- 📝 Obtenir un résumé d'un contenu
- ❓ Répondre aux questions fréquentes
- 🧭 S'orienter sur votre site

## 📊 Coûts

**Plugin** : 100% gratuit et open source

**API Mistral AI** : Pay-as-you-go
- Crédits gratuits pour tester (offerts par Mistral AI)
- Environ 0.001€ à 0.003€ par question
- Exemple : 1000 visiteurs × 2 questions = **6€ à 18€/mois**

Le coût est entièrement maîtrisable et transparent.

## 🛡️ Souveraineté et sécurité

- ✅ Vos données restent sur votre serveur WordPress
- ✅ Seules les requêtes nécessaires sont envoyées à Mistral AI
- ✅ Mistral AI est une entreprise française respectant le RGPD
- ✅ Aucune conversation n'est stockée de manière permanente
- ✅ Code 100% open source et auditable

## 🤝 Contribuer

Les contributions sont les bienvenues ! Consultez [CONTRIBUTING.md](CONTRIBUTING.md) pour plus d'informations.

### Développement local

```bash
# Cloner le dépôt
git clone https://github.com/votre-organisation/lydia-ia-plugin.git

# Créer un lien symbolique dans votre installation WordPress
ln -s /path/to/lydia-ia-plugin /path/to/wordpress/wp-content/plugins/lydia-ia-plugin
```

## 📝 Changelog

Consultez [CHANGELOG.md](CHANGELOG.md) pour l'historique des versions.

## 📄 License

Ce projet est sous licence [GPL v2](LICENSE) ou ultérieure.

## 🏢 À propos

Développé par [IA1](https://ia1.fr) - R2C SYSTEM SAS  
38 rue de la Blauderie – 79000 Niort  
Contact : [jc@ia1.fr](mailto:jc@ia1.fr) - 06 40 75 53 92

## 🙏 Remerciements

- [Mistral AI](https://mistral.ai) pour leur excellente API
- La communauté WordPress
- Tous les contributeurs

---

**Développé par IA1 • Propulsé par Mistral AI • Open Source & Souverain**
