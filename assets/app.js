import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


document.addEventListener('DOMContentLoaded', function() {

    console.log('Page chargée et script exécuté!');

    const detailBoard = document.querySelector("#detailBoard");
    let card = document.querySelector("#cardInput");

    console.log(card)

        if(card && detailBoard) {

            let cardId = card.value;

            console.log(cardId);
            fetch('https://api.scryfall.com/cards/' + cardId)
                        
                        // Convertit la réponse en format JSON
                        .then((response) => response.json())
                        
                        // Une fois que les données JSON sont disponibles
                        .then((data) => {
                            // console.log(data.name);
                            
                            let attributeName = document.createElement("div"); // ajoute les résultats à la barre Select
                            let attributeOracleText = document.createElement("div");
                            let attributeManaCost = document.createElement("div");
                            let attributeCMC = document.createElement("div");
                            let displayCard = new Image(287.5,402.5); // affiche les cartes reçues en résultat de la recherche 
                            if (data.image_uris && data.image_uris.normal) {
                                displayCard.src = data.image_uris.normal;
                            }

                            attributeName.innerHTML = data.name;
                            attributeOracleText.innerHTML = data.oracle_text
                            attributeManaCost.innerHTML = data.mana_cost
                            attributeCMC.innerHTML = data.cmc

                            detailBoard.appendChild(attributeName);
                            detailBoard.appendChild(displayCard);
                            detailBoard.appendChild(attributeOracleText);
                            detailBoard.appendChild(attributeManaCost);
                            detailBoard.appendChild(attributeCMC);
                        })
                        // .catch((error) => {
                        //     console.error('Erreur lors de la récupération des données de carte:', error);
                        // })
    }
});





