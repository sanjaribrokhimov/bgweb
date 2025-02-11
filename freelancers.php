<style>
    .product-info
    {
        height: 135px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        padding: 0 10px 8px;
    }
</style>

<div class="filter-container">
    <select class="form-select category-select" id="freelancerCategorySelect">
        <option value="">Все категории</option>
        <option value="webdev">Веб-разработка</option>
                            <option value="mobiledev">Мобильная разработка</option>
                            <option value="uidesign">UI/UX Дизайн</option>
                            <option value="graphicdesign">Графический дизайн</option>
                            <option value="marketing">Маркетинг</option>
                            <option value="smm">SMM</option>
                            <option value="copywriting">Копирайтинг</option>
                            <option value="translation">Перевод</option>
                            <option value="video">Видеопроизводство</option>
                            <option value="animation">Анимация</option>
                            <option value="voiceover">Озвучка</option>
                            <option value="photography">Фотография</option>
                            <option value="3d">3D моделирование</option>
                            <option value="gamedev">Разработка игр</option>
                            <option value="seo">SEO-оптимизация</option>
                            <option value="analytics">Аналитика</option>
                            <option value="consulting">Консультирование</option>
                            <option value="projectmanagement">Управление проектами</option>
    </select>
    <div class="filter-icon" id="freelancerFilterIcon">
        <i class="fas fa-filter"></i>
    </div>
</div>

<div class="products-grid">
    <!-- Карточки будут добавляться динамически -->
</div>
<script src="scripts/freelancers.js?v=1.0.1"></script> 
