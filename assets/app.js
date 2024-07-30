import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
console.log('hello')



    /**
     * Récupérer une liste de cartes de l'API scryfall.com
     */
    console.log('world')

const search = document.querySelector(".search")
const selectCard = document.querySelector(".card")
const board = document.querySelector(".board")

let button = document.getElementById("researchStart")

button.addEventListener('click', cardSearch);


function cardSearch() {

    button.addEventListener("click", () => {
        
        let value = search.value
        // console.log(value)

        selectCard.innerText = null 

        // fetch(`https://api.scryfall.com/cards/search?q=name:${value}+lang:fr`)
        fetch(`https://api.scryfall.com/cards/search?q=name:${value}`)
            // Convertit la réponse en format JSON
            .then((response) => response.json())
            
            // Une fois que les données JSON sont disponibles
            .then((recup) => {
            
                // console.log(recup["data"]);

                // enlève les cartes affichées de la précédente recherche
                const myNode = document.getElementById("cardBoard");
                while (myNode.firstChild) {
                    myNode.removeChild(myNode.lastChild);
                }

                recup["data"].forEach((card) => {
                
                    let option = document.createElement("option"); // ajoute les résultats à la barre Select
                    option.value = `${card.id}`
                    option.innerHTML = `${card.name}`

                    let displayCard = new Image(250,350); // affiche les cartes reçues en résultat de la recherche 
                    displayCard.src = `${card.image_uris.normal}`;
                    // displayCard.value = `${card.id}`
                    displayCard.id = `${card.id}` // attribution de l'id scryfall de la carte à l'id de l'élément html image généré
                    displayCard.classList.add('singleCard'); // on ajoute une classe singleCard si manipulation en css nécessaire

                    // Crée un élément <a> pour envelopper l'image
                    let link = document.createElement("a"); // on créé un élément lien
                    link.href = `${card.scryfall_uri}`; // on attribue le lien vers la carte au href du lien créé
                    link.appendChild(displayCard);//  l'image est ajoutée au lien
                
                    // console.log(displayCard.src)
                    selectCard.appendChild(option)
                    board.appendChild(link) // on ajoute le lien qui ajoute l'image


            })
        })
    }) 
}

//TODO cdn jquery -> warp




/**
 *  Fonction de prévisualisation des articles rédigés pour le site
 */
let btnPreview = document.querySelector('.open-preview');
btnPreview.addEventListener('click', e => {

    let title = document.querySelector('#article_form_articleTitle').value; // Get value of current form title input
    let text = document.querySelector('#article_form_articleText').value; //Get value of current form text input

    document.querySelector('.title').textContent = title; //Set title as content of preview div
    document.querySelector('.text').textContent = text; //Set text as content of preview div
    
    document.querySelector('.modal').classList.add('modal--open'); // Open modal
});

let btnClosePreview = document.querySelector('.close-preview');
btnClosePreview.addEventListener('click', e=> {
    document.querySelector('.modal').classList.remove('modal--open'); // Close modal   
});

let form = document.querySelector('article-form');
form.addEventListener('submit', e => {
    document.querySelector('.modal').classList.remove('modal--open'); // Hide modal
    // console.log('form submitted', form);
// Debugging only, prevent actual form submit, you might want to remove this later
// e.preventDefault();
// return false;
});

    





