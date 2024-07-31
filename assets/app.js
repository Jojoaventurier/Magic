import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// const search = document.querySelector("#searchId");
// const selectCard = document.querySelector("#cardId");
// const board = document.querySelector("#cardBoard");
// const researchStartButton = document.getElementById("researchStart");

// if (search && selectCard && board && researchStartButton) {
//     researchStartButton.addEventListener('click', cardSearch);
// }

// function cardSearch() {

//     let value = search.value
//     selectCard.innerText = null 

//     if(value) {

//         // fetch(`https://api.scryfall.com/cards/search?q=name:${value}+lang:fr`)
//         fetch(`https://api.scryfall.com/cards/search?q=name:${value}`)
//             // Convertit la réponse en format JSON
//             .then((response) => response.json())
            
//             // Une fois que les données JSON sont disponibles
//             .then((recup) => {

//                 // enlève les cartes affichées de la précédente recherche
//                 const myNode = document.getElementById("cardBoard");
//                 if (myNode) {
//                     while (myNode.firstChild) {
//                         myNode.removeChild(myNode.lastChild);
//                     }
//                 }

//                 recup["data"].forEach((card) => {
                
//                     let option = document.createElement("option"); // ajoute les résultats à la barre Select
//                     option.value = `${card.id}`
//                     option.innerHTML = `${card.name}`


//                     // Vérifier si card.image_uris et card.image_uris.normal existent
//                     if (card.image_uris && card.image_uris.normal) {
//                         let displayCard = new Image(250,350); // affiche les cartes reçues en résultat de la recherche 
//                         displayCard.src = `${card.image_uris.normal}`;
//                         displayCard.id = `${card.id}` // attribution de l'id scryfall de la carte à l'id de l'élément html image généré
//                         displayCard.classList.add('singleCard'); // on ajoute une classe singleCard si manipulation en css nécessaire

//                         // Crée un élément <a> pour envelopper l'image
//                         var cardDetailUrl = "{{ path('app_card_detail', {'cardId': 'REPLACE_CARD_ID' })}}";
//                         let link = document.createElement("a"); // on créé un élément lien
//                         let url = cardDetailUrl.replace('REPLACE_CARD_ID', card.id);
//                         link.href = url; // on attribue le lien vers la carte au href du lien créé pour renvoyer vers le détail de la carte
//                         // console.log(url)
//                         link.appendChild(displayCard);//  l'image est ajoutée au lien
//                         board.appendChild(link) // on ajoute le lien qui ajoute l'image
//                     }

//                     selectCard.appendChild(option)
//             });
//     })}

//         // .catch((error) => {
//         //     console.error('Erreur lors de la récupération des données de la carte :', error);
//         // });
// }




