import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');



    let btnPreview = document.querySelector('.open-preview')
    btnPreview.addEventListener('click', e => {

        let title = document.querySelector('#article_form_articleTitle').value; // Get value of current form title input
        let text = document.querySelector('#article_form_articleText').value //Get value of current form text input

        document.querySelector('.title').textContent = title; //Set title as content of preview div
        document.querySelector('.text').textContent = text; //Set text as content of preview div
        
        document.querySelector('.modal').classList.add('modal--open'); // Open modal
    });

    let btnClosePreview = document.querySelector('.close-preview')
    btnClosePreview.addEventListener('click', e=> {
        document.querySelector('.modal').classList.remove('modal--open'); // Close modal   
    })

    let form = document.querySelector('article-form');
    form.addEventListener('submit', e => {
        document.querySelector('.modal').classList.remove('modal--open'); // Hide modal
        console.log('form submitted', form);
    
    // Debugging only, prevent actual form submit, you might want to remove this later
    e.preventDefault();
    return false;

    })




