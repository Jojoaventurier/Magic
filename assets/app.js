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
     * Récupérer une carte de l'API scryfall.com
     */
    console.log('world')
const search = document.querySelector(".search")
const selectCard = document.querySelector(".card")

search.addEventListener("input", () => {
    // Récupère la valeur d'entrée dans le champ de code postal
    let value = search.value
    console.log(value)
    // Vide la contenu actuel de la liste de sélection de ville
    selectCard.innerText = null 
    // Effectue une requête fetch vers l'API de géolocalisation avec le code postal saisi
    // fetch(`https://api.scryfall.com/cards/search?q=name:${value}+lang:fr`)
    fetch(`https://api.scryfall.com/cards/search?q=name:${value}`)
        // Convertit la réponse en format JSON
        .then((response) => response.json())
        
        // Une fois que les données JSON sont disponibles
        .then((list) => {
            // Affiche les données dans la console (pour debug si besoin)
            console.log(list);
             // Parcours chaque objet "ville" dans les données récupérées
            list.data.forEach((object) => {
                // Créé un nouvel élément d'option HTML
                let option = document.createElement("option")
                // Définit la valeur de l'option comme le code de la ville
                option.value = `${data.id}`
                // Définit le texte affiché de l'option comme le nom de la ville
                option.innerHTML = `${data.name}`
                // Ajoute l'option à la liste de sélection de ville
                selectCard.appendChild(option)

            })
        })
    }) 


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


    





