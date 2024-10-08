import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() { // le script se déclenche uniquement une fois le DOM chargé

    const searchInput = document.getElementById('searchId'); //on récupère l'élément qui sert de barre de recherche à l'utilisateur
    const cardSelect = document.getElementById('cardSelect'); // on récupère l'élément HTML select
    const researchButton = document.getElementById('researchStart'); // on récupère le bouton qui doit déclenche la recherche
    const cardBoard = document.getElementById('cardBoard'); // récupère la div dans laquelle les images et lien vers le détail des cartes récupérées seront insérées

    researchButton.addEventListener('click', function() { // on ajoute une fonction qui se déclenche au clic de l'utilisateur sur le bouton qui doit déclencher la recherche
        const query = searchInput.value; // on attribue la valeur saisie par l'utilisateur à une variable query qu'on va utiliser dans la requête envers l'api

        //requête à l'api selon la recherche de l'utilisateur
        // fetch(`https://api.scryfall.com/cards/search?q=name:${query}+lang:fr`) recherche pour une carte en français spécifiquement
        fetch(`https://api.scryfall.com/cards/search?q=is:commander+name:${query}`) //+type:legendary+type:creature
            // Convertit la réponse en format JSON
            .then(response => response.json())
            // Une fois que les données JSON sont disponibles
            .then(data => {
             //   console.log(data); Vérifie le format des données

                if (data.data && Array.isArray(data.data)) { // on vérifiqe qu'on récupère bien des données de l'api, et quelle soit sous forme de tableau

                    const myNode = document.getElementById("cardBoard"); // permet de réinitialiser l'affichage des cartes, si l'utilisateur appuie une nouvelle fois sur le bouton de recherche, les cartes affichées précédemment sont enlevées
                        if (myNode) {
                            while (myNode.firstChild) {// tant que cardBoard a des enfants (les cartes que l'on affiche)
                                myNode.removeChild(myNode.lastChild); // on enlève le dernier enfant de cardBoard jusqu'à ce qu'il n'y en ait plus
                            }
                        }
                        
                    cardSelect.innerHTML = ''; // Supprime les entrées précédentes

                    data.data.forEach(card => {
                        const option = document.createElement('option'); // rajoute une option au select pour chaque carte récupérée depuis l'API
                        option.value = card.id;
                        option.textContent = card.name;
                        cardSelect.appendChild(option);
                            console.log(card)

                        if (card.image_uris && card.image_uris.normal) {
                            console.log('affichage carte')
                            let displayCard = new Image(250,350); // créé un nouvel élément <img> d'une taille fixe
                            displayCard.src = `${card.image_uris.normal}`; // j'attribue la source de l'image à l'url correspondant à l'image que je souhaite afficher (plusieurs tailles possibles)
                            displayCard.id = `${card.id}` // attribution de l'id scryfall de la carte à l'id de l'élément html image généré
                            displayCard.classList.add('singleCard'); // on ajoute une classe singleCard si manipulation en css nécessaire
    
                            // Crée un élément <a> pour envelopper l'image
                            var cardDetailUrl = "{{ path('app_card_detail', {'cardId': 'REPLACE_CARD_ID' })}}"; //on attribue le chemin vers le détail de la carte à la variable cardDetailUrl
                            let link = document.createElement("a"); // on créé un élément <link> que l'on attribue à la variable link
                            let url = cardDetailUrl.replace('REPLACE_CARD_ID', card.id);
                            link.href = url; // on attribue le lien vers la carte au href du de l'élément HTML créé pour renvoyer vers le détail de la carte

                            link.appendChild(displayCard);//  l'image est ajoutée au lien créé précédemment
                           
                            cardBoard.appendChild(link) // on ajoute le lien à la div qui affiche les carte (le l'image est ajoutée au lien, qui lui même est ajouté à la div cardBoard)
                        
                        } else if(card.card_faces[0]['image_uris'] && card.card_faces[0]['image_uris']['normal']) { // récupère la premiere face de la carte double face sinon ne s'affichent
                            
                            let displayCard = new Image(250,350); 
                            displayCard.src = `${card.card_faces[0]['image_uris']['normal']}`;
                            displayCard.id = `${card.id}` 
                            displayCard.classList.add('singleCard'); 
    
                            var cardDetailUrl = "{{ path('app_card_detail', {'cardId': 'REPLACE_CARD_ID' })}}"; 
                            let link = document.createElement("a"); 
                            let url = cardDetailUrl.replace('REPLACE_CARD_ID', card.id);
                            link.href = url; 

                            link.appendChild(displayCard);//  l'image est ajoutée au lien créé précédemment
                            cardBoard.appendChild(link)
                        }
                    });
                } else {
                    console.error('Erreur: la réponse de l\'API ne contient pas un tableau de cartes dans "data"');
                }
            })
            .catch(error => console.error('Erreur:', error));
    });


});





