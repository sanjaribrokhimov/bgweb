// Get user ads
const getUserAds = async (category, userId) => {
    try {
        const response = await fetch(`https://bgweb.nurali.uz/api/ads/user/${category}/${userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        if (response.ok) {
            console.log('User ads:', data);
            return data;
        } else {
            console.error('Error:', data.error);
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Error getting ads:', error);
        throw error;
    }
};

// Delete ad
const deleteAd = async (type, adId) => {
    try {
        const response = await fetch(`https://bgweb.nurali.uz/api/ads/${type}/${adId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        if (response.ok) {
            console.log('Successfully deleted:', data.message);
            return data;
        } else {
            console.error('Error:', data.error);
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Error deleting:', error);
        throw error;
    }
};

document.addEventListener('DOMContentLoaded', async () => {
    let currentLang = localStorage.getItem('selectedLanguage') || 'ru';
    let t = translations[currentLang];
    
    const loadingIndicator = document.getElementById('loadingIndicator');
    const adsContainer = document.getElementById('adsContainer');
    const noAdsMessage = document.getElementById('noAdsMessage');
    
    const userCategory = localStorage.getItem('category');
    const userId = localStorage.getItem('userId');

    // Функция обновления текстов
    const updateTexts = () => {
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            if (t[key]) {
                element.textContent = t[key];
            }
        });

        // Обновляем тексты в модальном окне
        document.querySelector('#deleteConfirmModal .modal-title').textContent = t.myads.deleteConfirmTitle;
        document.querySelector('#deleteConfirmModal .modal-body p.mb-0').textContent = t.myads.deleteConfirmText;
        document.querySelector('#deleteConfirmModal .modal-body p.text-muted').textContent = t.myads.deleteConfirmSubtext;
        document.querySelector('#deleteConfirmModal #confirmDelete').textContent = t.myads.delete;
        document.querySelector('#deleteConfirmModal [data-bs-dismiss="modal"]').textContent = t.myads.cancel;
    };
    
    // Слушатель события смены языка
    document.addEventListener('languageChanged', () => {
        currentLang = localStorage.getItem('selectedLanguage');
        t = translations[currentLang];
        updateTexts();
        // Перерисовываем объявления с новыми переводами
        displayAds(currentAds);
    });

    // Display ads function
    const displayAds = (ads) => {
        adsContainer.innerHTML = '';
        
        if (ads.length === 0) {
            noAdsMessage.style.display = 'block';
            return;
        }
        
        ads.forEach(ad => {
            const adCard = document.createElement('div');
            adCard.className = 'product-card';
            adCard.innerHTML = `
                <div class="product-image">
                    <img src="${ad.photo_base64}" alt="${ad.nickname}">
                </div>
                <div class="product-info">
                    <h3>${ad.nickname || ad.name}</h3>
                    <p>${ad.ad_comment}</p>
                    <button class="btn btn-danger delete-btn" data-id="${ad.ID}">
                        <i class="fas fa-trash"></i> ${t.myads.delete}
                    </button>
                </div>
            `;
            adsContainer.appendChild(adCard);
        });
    };

    let currentAds = []; // Сохраняем объявления для перерисовки при смене языка

    // Load ads
    try {
        loadingIndicator.style.display = 'block';
        currentAds = await getUserAds(userCategory, userId);
        displayAds(currentAds);
    } catch (error) {
        console.error('Error loading ads:', error);
        adsContainer.innerHTML = `<p class="error-message">${t.myads.deleteError}</p>`;
    } finally {
        loadingIndicator.style.display = 'none';
    }

    // Handle delete
    let currentDeleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

    adsContainer.addEventListener('click', async (e) => {
        if (e.target.closest('.delete-btn')) {
            const deleteBtn = e.target.closest('.delete-btn');
            currentDeleteId = deleteBtn.dataset.id;
            deleteModal.show();
        }
    });

    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            await deleteAd(userCategory, currentDeleteId);
            deleteModal.hide();
            
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            successAlert.style.zIndex = '1050';
            successAlert.innerHTML = `
                ${t.myads.deleteSuccess}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(successAlert);
            
            setTimeout(() => {
                successAlert.remove();
                window.location.reload();
            }, 3000);
            
        } catch (error) {
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            errorAlert.style.zIndex = '1050';
            errorAlert.innerHTML = `
                ${t.myads.deleteError}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.body.appendChild(errorAlert);
            
            setTimeout(() => {
                errorAlert.remove();
            }, 3000);
        }
    });
}); 