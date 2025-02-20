import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const thumbnails = document.querySelectorAll('.thumbnail'); // Toutes les vignettes
    const previewImage = document.getElementById('previewImage'); // Zone pour les images
    const previewVideo = document.getElementById('previewVideo'); // Zone pour les vidéos
    const videoSource = document.getElementById('videoSource'); // Source vidéo dans le preview
    const thumbnailContainer = document.getElementById('thumbnailContainer'); // Conteneur des vignettes
    const arrowLeft = document.getElementById('arrowLeft'); // Flèche gauche
    const arrowRight = document.getElementById('arrowRight'); // Flèche droite

    // Fonction pour afficher une image dans la preview
    const showImage = (src, thumbnail) => {
        previewImage.src = src; // Charger l'image
        previewImage.classList.remove('d-none'); // Afficher l'image
        previewVideo.classList.add('d-none'); // Cacher la vidéo
        videoSource.src = ''; // Réinitialiser la source vidéo
        highlightThumbnail(thumbnail); // Ajouter un cadre autour de la vignette sélectionnée

    };

    // Fonction pour afficher une vidéo dans la preview
    const showVideo = (src, thumbnail) => {
        videoSource.src = src; // Mettre à jour la source vidéo
        previewVideo.load(); // Recharger la vidéo pour qu'elle fonctionne
        previewVideo.classList.remove('d-none'); // Afficher la vidéo
        previewImage.classList.add('d-none'); // Cacher l'image
        highlightThumbnail(thumbnail); // Ajouter un cadre autour de la vignette sélectionnée
    };

    const highlightThumbnail = (selectedThumbnail) => {
        thumbnails.forEach((thumbnail) => {
            thumbnail.classList.remove('selected-thumbnail'); // Supprimer la classe des autres
        });
        selectedThumbnail.classList.add('selected-thumbnail'); // Ajouter la classe à la vignette sélectionnée
    };
    // Gestion des clics sur les vignettes
    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            const type = thumbnail.dataset.type; // Type : 'image' ou 'video'
            const src = thumbnail.dataset.src; // Source de l'image ou vidéo

            if (type === 'image') {
                showImage(src, thumbnail); // Afficher une image
            } else if (type === 'video') {
                showVideo(src, thumbnail); // Afficher une vidéo
            }
        });
    });

    // Initialisation : charger la première vignette
    if (thumbnails.length > 0) {
        const firstThumbnail = thumbnails[0];
        const type = firstThumbnail.dataset.type;
        const src = firstThumbnail.dataset.src;

        if (type === 'image') {
            showImage(src, firstThumbnail); // Afficher la première image
        } else if (type === 'video') {
            showVideo(src, firstThumbnail); // Afficher la première vidéo
        }
    }

    // Gestion du défilement des vignettes avec les flèches
    arrowLeft.addEventListener('click', () => {
        thumbnailContainer.scrollBy({ left: -200, behavior: 'smooth' });
    });

    arrowRight.addEventListener('click', () => {
        thumbnailContainer.scrollBy({ left: 200, behavior: 'smooth' });
    });
});

