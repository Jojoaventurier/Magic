Magic-Hub

Magic-Hub est une plateforme web dédiée aux joueurs de Magic : The Gathering. Ce projet a été conçu dans le cadre de mon diplôme en développement web et a pour objectif de fournir une solution intuitive, performante et communautaire pour les passionnés du jeu.

<section>
![Frame 3](https://github.com/user-attachments/assets/cd99b025-e507-436d-a66a-153f33f97650)
![Frame 4](https://github.com/user-attachments/assets/0e6af850-7662-491f-ac8c-f7a7efb42ea5)
  
<h2>Fonctionnalités principales</h2>
    
<article>
    <h3>Gestion de Decks</h3>
    <ul>
        <li>Créer, modifier et supprimer des decks.</li>
        <li>Importer des decks via un fichier texte (à l'aide de l'API Scryfall).</li>
        <li>Visualisation des détails du deck : nom, format, créateur, description, etc.</li>
        <li>Affichage de statistiques affichées à l'aide de chart.js</li>
        <li>Organisation des cartes par liste principale, Sideboard et Maybeboard.</li>
        <li>Export des decks au format <code>.txt</code> ou <code>.csv</code>.</li>
    </ul>
    ![Frame 5](https://github.com/user-attachments/assets/58399071-7b5e-488b-b1d9-310577c25ae1)
    ![Frame 2](https://github.com/user-attachments/assets/fe55a4aa-5d43-44b4-b813-ea680d101c95)
    ![Capture d’écran 2025-01-07 181151](https://github.com/user-attachments/assets/ca443f44-e2e1-4200-ae9d-505247dc156d)
    ![Frame 1](https://github.com/user-attachments/assets/8b696887-7442-4293-b1e4-de9f7a4cad7c)
    ![Capture d'écran 2024-10-18 103019](https://github.com/user-attachments/assets/567ec75b-a90e-411d-aa29-f104cd4cbe1b)
    ![Capture d'écran 2024-10-18 103101](https://github.com/user-attachments/assets/f44d89c1-4f14-4214-b212-e17f1f52709a)

</article>

<article>
    <h3>Recherche de Decks</h3>
    <ul>
        <li>Recherche et exploration des decks publics.</li>
        <li>Filtres avancés : par nom, format, couleur(s)</li>
        <li>Affichage des informations clés : nombre de cartes, format, couleurs principales, etc.</li>
    </ul>
    ![Frame 6](https://github.com/user-attachments/assets/355a05dd-ce5a-420b-9ba5-304d97746789)

</article>

<article>
    <h3>Moteur de Recherche de Cartes</h3>
    <ul>
        <li>Recherche avancée basée sur l'API Scryfall : par nom, rareté, type, couleurs, set, etc.</li>
        <li>Affichage des détails des cartes : légalité selon les formats, rang EDHREC, prix, et autres versions.</li>
        <li>Intégrations externes : redirections vers EDHREC, TCGPlayer, CardMarket.</li>
    </ul>
    ![Frame 7](https://github.com/user-attachments/assets/2b1878e7-fa1b-47c6-a7c3-59173fd1a16c)
    ![Frame 8](https://github.com/user-attachments/assets/9f854e8f-baf3-4f2b-b07e-cbec3a3364c4)
    ![Capture d'écran 2024-10-09 204426](https://github.com/user-attachments/assets/8b7f4b71-85b4-4727-8ae5-859a46552824)
    ![Frame 13](https://github.com/user-attachments/assets/01c19fc5-6451-457d-a19e-fd5cddfc1022)

</article>

<article>
    <h3>Communauté et Interaction</h3>
    <ul>
        <li>Profils utilisateurs avec pseudonyme, avatar, description, abonnés/abonnements.</li>
        <li>Possibilité de partager un live Twitch directement sur le profil.</li>
        <li>Forum avec création de sujets et messages, recherche et signalements.</li>
        <li>Messagerie privée avec notifications et système de gestion des conversations.</li>
    </ul>
    ![Frame 12](https://github.com/user-attachments/assets/89d5e706-3cd4-494b-99b6-58549c30ab3e)
    ![Frame 10](https://github.com/user-attachments/assets/3d2a48b3-1d23-4460-b370-55ab130d0636)
    ![Capture d'écran 2024-09-16 003751](https://github.com/user-attachments/assets/7e5d64c1-6334-42e5-9488-b736c9a18034)
    ![Capture d'écran 2024-10-18 112105](https://github.com/user-attachments/assets/c9dcbb39-d3b9-4e2a-8320-3575b5bb6b84)
    ![Capture d'écran 2024-10-18 112228](https://github.com/user-attachments/assets/53457cf2-d694-4bb1-9379-b04ddff2fa0c)
    ![Capture d’écran 2025-01-07 181905](https://github.com/user-attachments/assets/b89b9aa4-8d02-475e-a180-83b5a821917d)
    ![Capture d'écran 2024-09-16 003751](https://github.com/user-attachments/assets/562959ab-1b76-4e90-b72b-dbe3f0ac6c68)
</article>

<article>
    <h3>Modération et Administration</h3>
    <ul>
        <li>Signalement de contenus (commentaires, utilisateurs).</li>
        <li>Actions de modération : bannissements, avertissements.</li>
        <li>Gestion des droits d'accès pour les utilisateurs.</li>
    </ul>
</article>

<article>
    <h3>Intégrations et API</h3>
    <ul>
        <li><strong>API Scryfall</strong> : recherche et gestion des cartes Magic.</li>
        <li><strong>Google API</strong> : gestion de l'authentification OAuth2.</li>
    </ul>
</article>
</section>

<section>
<h2>Objectif du projet</h2>
<p>L'objectif principal de Magic-Hub est de proposer un outil complet pour :</p>
<ul>
    <li>Gérer les collections de cartes.</li>
    <li>Construire et améliorer des decks.</li>
    <li>Interagir avec une communauté de joueurs passionnés.</li>
</ul>
</section>

<section>
<h2>Technologies utilisées</h2>
<ul>
    <li><strong>Backend</strong> : Symfony (PHP), base de données MySQL, Doctrine.</li>
    <li><strong>Frontend</strong> : Tailwind CSS, Vanilla JavaScript, Alpine.js, Charts.js</li>
    <li><strong>API externes</strong> : Scryfall, Google OAuth2.</li>
</ul>
</section>
