Magic-Hub

Magic-Hub est une plateforme web dédiée aux joueurs de Magic : The Gathering. Ce projet a été conçu dans le cadre de mon diplôme en développement web et a pour objectif de fournir une solution intuitive, performante et communautaire pour les passionnés du jeu.

<section>
    <h2>Fonctionnalités principales</h2>

<article>
    <h3>Gestion de Decks</h3>
    <ul>
        <li>Créer, modifier et supprimer des decks.</li>
        <li>Importer des decks via un fichier texte (à l'aide de l'API Scryfall).</li>
        <li>Visualisation des détails du deck : nom, format, créateur, description, etc.</li>
        <li>Organisation des cartes par liste principale, Sideboard et Maybeboard.</li>
        <li>Export des decks au format <code>.txt</code> ou <code>.csv</code>.</li>
    </ul>
</article>

<article>
    <h3>Recherche de Decks</h3>
    <ul>
        <li>Recherche et exploration des decks publics.</li>
        <li>Filtres avancés : par nom, format, couleur(s), commandant, etc.</li>
        <li>Affichage des informations clés : nombre de cartes, format, couleurs principales, etc.</li>
        <li>Affichage de statistiques affichées à l'aide de chart.JS</li>
    </ul>
</article>

<article>
    <h3>Moteur de Recherche de Cartes</h3>
    <ul>
        <li>Recherche avancée basée sur l'API Scryfall : par nom, rareté, type, couleurs, set, etc.</li>
        <li>Affichage des détails des cartes : légalité selon les formats, rang EDHREC, prix, et autres versions.</li>
        <li>Intégrations externes : redirections vers EDHREC, TCGPlayer, CardMarket.</li>
    </ul>
</article>

<article>
    <h3>Communauté et Interaction</h3>
    <ul>
        <li>Profils utilisateurs avec pseudonyme, avatar, description, abonnés/abonnements.</li>
        <li>Possibilité de partager un live Twitch directement sur le profil.</li>
        <li>Forum avec création de sujets et messages, recherche et signalements.</li>
        <li>Messagerie privée avec notifications et système de gestion des conversations.</li>
    </ul>
</article>

<article>
    <h3>Modération et Administration</h3>
    <ul>
        <li>Signalement de contenus (commentaires, utilisateurs).</li>
        <li>Actions de modération : bannissements, avertissements.</li>
        <li>Gestion des droits d'accès pour les utilisateurs.</li>
        <li>Panneau d'administration complet.</li>
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
    <li><strong>Backend</strong> : Symfony (PHP), base de données MySQL.</li>
    <li><strong>Frontend</strong> : Tailwind CSS, Vanilla JavaScript, Alpine.js, Charts.js</li>
    <li><strong>API externes</strong> : Scryfall, Google OAuth2.</li>
    <li><strong>Environnement</strong> : Docker pour la gestion des conteneurs.</li>
</ul>
</section>
