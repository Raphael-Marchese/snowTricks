import './bootstrap.js';
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const previewImage = document.getElementById('previewImage');
    const previewVideo = document.getElementById('previewVideo');
    const videoSource = document.getElementById('videoSource');
    const thumbnailContainer = document.getElementById('thumbnailContainer');
    const arrowLeft = document.getElementById('arrowLeft');
    const arrowRight = document.getElementById('arrowRight');

    const showImage = (src, thumbnail) => {
        previewImage.src = src;
        previewImage.classList.remove('d-none');
        previewVideo.classList.add('d-none');
        videoSource.src = '';
        highlightThumbnail(thumbnail);

    };

    const showVideo = (src, thumbnail) => {
        videoSource.src = src;
        previewVideo.load();
        previewVideo.classList.remove('d-none');
        previewImage.classList.add('d-none');
        highlightThumbnail(thumbnail);
    };

    const highlightThumbnail = (selectedThumbnail) => {
        thumbnails.forEach((thumbnail) => {
            thumbnail.classList.remove('selected-thumbnail');
        });
        selectedThumbnail.classList.add('selected-thumbnail');
    };
    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            const type = thumbnail.dataset.type;
            const src = thumbnail.dataset.src;

            if (type === 'image') {
                showImage(src, thumbnail);
            } else if (type === 'video') {
                showVideo(src, thumbnail);
            }
        });
    });

    if (thumbnails.length > 0) {
        const firstThumbnail = thumbnails[0];
        const type = firstThumbnail.dataset.type;
        const src = firstThumbnail.dataset.src;

        if (type === 'image') {
            showImage(src, firstThumbnail);
        } else if (type === 'video') {
            showVideo(src, firstThumbnail);
        }
    }

    arrowLeft.addEventListener('click', () => {
        thumbnailContainer.scrollBy({ left: -200, behavior: 'smooth' });
    });

    arrowRight.addEventListener('click', () => {
        thumbnailContainer.scrollBy({ left: 200, behavior: 'smooth' });
    });
});

