// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
// start the Stimulus application
import './bootstrap';


// Function to show password 



// Function to fetch random recipe details
async function fetchRandomRecipeDetails() {
    const response = await fetch('https://www.themealdb.com/api/json/v1/1/random.php');
    const data = await response.json();
    return data.meals[0];
}

// Function to build carousel items with recipe details
async function buildCarouselItems() {
    const carouselInner = document.querySelector('.carousel-inner');
    const carouselIndicators = document.querySelector('.carousel-indicators');

    for (let i = 0; i < 3; i++) { // Display 3 random recipes
        const recipe = await fetchRandomRecipeDetails();

        const item = document.createElement('div');
        item.classList.add('carousel-item');
        if (i === 0) {
            item.classList.add('active');
        }

        const img = document.createElement('img');
        img.src = recipe.strMealThumb;
        img.alt = recipe.strMeal;
        img.classList.add('d-block');

        const caption = document.createElement('div');
        caption.classList.add('carousel-caption', 'd-none', 'd-md-block');
        const title = document.createElement('h5');
        title.textContent = recipe.strMeal;

        const areaLabel = document.createElement('p');
        areaLabel.textContent = `From: ${recipe.strArea}`;

        const anchorLink = document.createElement('a');
        anchorLink.href = `${recipe.strSource}`;
        anchorLink.textContent = 'View Recipe Details';

        caption.appendChild(title);
        caption.appendChild(areaLabel);
        caption.appendChild(anchorLink);

        item.appendChild(img);
        item.appendChild(caption);

        carouselInner.appendChild(item);

        // Create and add indicators
        const indicator = document.createElement('button');
        indicator.type = 'button';
        indicator.dataset.bsTarget = '#carouselExampleCaptions';
        indicator.dataset.bsSlideTo = i.toString();
        if (i === 0) {
            indicator.classList.add('active');
        }
        carouselIndicators.appendChild(indicator);
    }
}

// Fonction pour activer et faire défiler le carrousel
function activateCarousel() {
    const carousel = new bootstrap.Carousel(document.querySelector('#carouselExampleCaptions'), {
        interval: 5000, // Intervalle de temps en millisecondes entre chaque diapositive
    });
}

// Appeler la fonction pour construire les éléments du carrousel
buildCarouselItems();

// Appeler la fonction pour activer le carrousel
activateCarousel();


// import './bootstrap.js';

