<style>
    .product-info
    {
        height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        padding: 0 10px 10px;
    }
</style>

<div class="filter-container">
    <select class="form-select category-select" id="freelancerCategorySelect">
        <option class="translate" value="">Все категории</option>
        <option class="translate" value="webdev">Веб-разработка</option>
        <option class="translate" value="mobiledev">Мобильная разработка</option>
        <option class="translate" value="uidesign">UI/UX Дизайн</option>
        <option class="translate" value="graphicdesign">Графический дизайн</option>
        <option class="translate" value="marketing">Маркетинг</option>
        <option class="translate" value="smm">SMM</option>
        <option class="translate" value="copywriting">Копирайтинг</option>
        <option class="translate" value="translation">Перевод</option>
        <option class="translate" value="video">Видеопроизводство</option>
        <option class="translate" value="animation">Анимация</option>
        <option class="translate" value="voiceover">Озвучка</option>
        <option class="translate" value="photography">Фотография</option>
        <option class="translate" value="3d">3D моделирование</option>
        <option class="translate" value="gamedev">Разработка игр</option>
        <option class="translate" value="seo">SEO-оптимизация</option>
        <option class="translate" value="analytics">Аналитика</option>
        <option class="translate" value="consulting">Консультирование</option>
        <option class="translate" value="projectmanagement">Управление проектами</option>
    </select>
    <div class="filter-icon" id="freelancerFilterIcon">
        <i class="fas fa-filter"></i>
    </div>
</div>

<div class="products-grid">
    <!-- Карточки будут добавляться динамически -->
</div>
