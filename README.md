# Magic-Hub

Magic-Hub est une plateforme web dédiée aux joueurs de Magic : The Gathering. Ce projet a été conçu dans le cadre de mon diplôme en développement web et a pour objectif de fournir une solution intuitive, performante et communautaire pour les passionnés du jeu.
![Frame 3](https://github.com/user-attachments/assets/93565a35-1a61-4661-a97a-863ceff3411e)


## Fonctionnalités principales

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

![Frame 1](https://github.com/user-attachments/assets/02c8368b-b0f2-45a8-8287-8bd6fa4a2adc)
![Capture d'écran 2024-10-18 103019](https://github.com/user-attachments/assets/31a2ef7a-c130-4ac3-a25f-e1e68820eb0d)
![Capture d'écran 2024-10-18 103101](https://github.com/user-attachments/assets/906b861f-6954-435a-b496-599762344717)

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
![Frame 13](https://github.com/user-attachments/assets/a581afa6-55d6-4f41-b32d-0d873be22a4f)
![Capture d'écran 2024-10-09 204426](https://github.com/user-attachments/assets/de807276-f56b-416e-b1c5-ce3bd751eda1)

---

### Communauté et Interaction
- Profils utilisateurs avec pseudonyme, avatar, description, abonnés/abonnements.
- Possibilité de partager un live Twitch directement sur le profil.
- Forum avec création de sujets et messages, recherche et signalements.
- Messagerie privée avec notifications et système de gestion des conversations.

![Frame 12](https://github.com/user-attachments/assets/e700c4c3-d07e-488d-83bb-9ec66f1daca6)
![Frame 10](https://github.com/user-attachments/assets/bc5ce6e9-1ab3-41f9-b9bc-bb29ea1b4d4c)
![Capture d'écran 2024-09-16 003751](https://github.com/user-attachments/assets/c5810e93-23a9-44f2-93d6-d4db513686da)
![Capture d’écran 2025-01-07 181905](https://github.com/user-attachments/assets/e6fa77e8-c09a-4c84-bf55-2362c86af3aa)
![Capture d'écran 2024-10-18 111714](https://github.com/user-attachments/assets/9077ed62-92b9-4602-8628-618824b9e5f2)
![Capture d'écran 2024-10-18 112105](https://github.com/user-attachments/assets/a69ab811-84d5-4cbe-a94c-a9e4c517f7cb)
![Capture d'écran 2024-10-18 112228](https://github.com/user-attachments/assets/5a76a1fb-97cf-49cc-9d44-5717db2b8cb2)
![Frame 9](https://github.com/user-attachments/assets/5aac4d5d-0110-48f1-8092-51fc63d47ae6)
![Frame 14](https://github.com/user-attachments/assets/abe94151-00a4-4173-bf7c-e5493ade05d8)


---

### Modération et Administration
- Signalement de contenus (commentaires, utilisateurs).
- Actions de modération : bannissements, avertissements.
- Gestion des droits d'accès pour les utilisateurs.

---

### Intégrations et API
- **API Scryfall** : recherche et gestion des cartes Magic.
- **Google API** : gestion de l'authentification OAuth2.

![Frame 4](https://github.com/user-attachments/assets/76a9ddc7-d52c-490f-892b-cbeed07f3b61)

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

