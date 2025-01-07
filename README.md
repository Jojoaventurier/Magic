# Magic-Hub

Magic-Hub est une plateforme web dédiée aux joueurs de Magic : The Gathering. Ce projet a été conçu dans le cadre de mon diplôme en développement web et a pour objectif de fournir une solution intuitive, performante et communautaire pour les passionnés du jeu.

## Fonctionnalités principales
![Frame 4](https://github.com/user-attachments/assets/76a9ddc7-d52c-490f-892b-cbeed07f3b61)
![Frame 3](https://github.com/user-attachments/assets/93565a35-1a61-4661-a97a-863ceff3411e)

### Gestion de Decks
- Créer, modifier et supprimer des decks.
- Importer des decks via un fichier texte (à l'aide de l'API Scryfall).
- Visualisation des détails du deck : nom, format, créateur, description, etc.
- Affichage de statistiques affichées à l'aide de chart.js.
- Organisation des cartes par liste principale, Sideboard et Maybeboard.
- Export des decks au format `.txt` ou `.csv`.

![Frame 5](https://github.com/user-attachments/assets/58399071-7b5e-488b-b1d9-310577c25ae1)
![Frame 2](https://github.com/user-attachments/assets/fe55a4aa-5d43-44b4-b813-ea680d101c95)
![Capture d’écran 2025-01-07 181151](https://github.com/user-attachments/assets/ca443f44-e2e1-4200-ae9d-505247dc156d)

---

### Recherche de Decks
- Recherche et exploration des decks publics.
- Filtres avancés : par nom, format, couleur(s).
- Affichage des informations clés : nombre de cartes, format, couleurs principales, etc.

![Frame 6](https://github.com/user-attachments/assets/355a05dd-ce5a-420b-9ba5-304d97746789)

---

### Moteur de Recherche de Cartes
- Recherche avancée basée sur l'API Scryfall : par nom, rareté, type, couleurs, set, etc.
- Affichage des détails des cartes : légalité selon les formats, rang EDHREC, prix, et autres versions.
- Intégrations externes : redirections vers EDHREC, TCGPlayer, CardMarket.

![Frame 7](https://github.com/user-attachments/assets/2b1878e7-fa1b-47c6-a7c3-59173fd1a16c)
![Frame 8](https://github.com/user-attachments/assets/9f854e8f-baf3-4f2b-b07e-cbec3a3364c4)

---

### Communauté et Interaction
- Profils utilisateurs avec pseudonyme, avatar, description, abonnés/abonnements.
- Possibilité de partager un live Twitch directement sur le profil.
- Forum avec création de sujets et messages, recherche et signalements.
- Messagerie privée avec notifications et système de gestion des conversations.

![Frame 12](https://github.com/user-attachments/assets/89d5e706-3cd4-494b-99b6-58549c30ab3e)

---

### Modération et Administration
- Signalement de contenus (commentaires, utilisateurs).
- Actions de modération : bannissements, avertissements.
- Gestion des droits d'accès pour les utilisateurs.

---

### Intégrations et API
- **API Scryfall** : recherche et gestion des cartes Magic.
- **Google API** : gestion de l'authentification OAuth2.

---

## Objectif du projet

L'objectif principal de Magic-Hub est de proposer un outil complet pour :
- Gérer les collections de cartes.
- Construire et améliorer des decks.
- Interagir avec une communauté de joueurs passionnés.

---

## Technologies utilisées

- **Backend** : Symfony (PHP), base de données MySQL, Doctrine.
- **Frontend** : Tailwind CSS, Vanilla JavaScript, Alpine.js, Charts.js.
- **API externes** : Scryfall, Google OAuth2.

