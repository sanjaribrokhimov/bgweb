<style>
    .product-info
    {
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        padding: 0 10px 10px;
    }
</style>

<div class="filter-container">
    <select class="form-select category-select" id="companyCategorySelect">
        <option class="translate" value="">Все категории</option>
        <option class="translate" value="retail">Розничная торговля</option>
        <option class="translate" value="wholesale">Оптовая торговля</option>
        <option class="translate" value="services">Услуги и сервис</option>
        <option class="translate" value="manufacturing">Производство</option>
        <option class="translate" value="tech">IT и технологии</option>
        <option class="translate" value="finance">Финансы и банкинг</option>
        <option class="translate" value="construction">Строительство</option>
        <option class="translate" value="realestate">Недвижимость</option>
        <option class="translate" value="healthcare">Здравоохранение</option>
        <option class="translate" value="education">Образование</option>
        <option class="translate" value="hospitality">Гостиничный бизнес</option>
        <option class="translate" value="restaurant">Рестораны и общепит</option>
        <option class="translate" value="logistics">Логистика</option>
        <option class="translate" value="agriculture">Сельское хозяйство</option>
        <option class="translate" value="energy">Энергетика</option>
        <option class="translate" value="media">Медиа и развлечения</option>
        <option class="translate" value="consulting">Консалтинг</option>
        <option class="translate" value="automotive">Автомобильный бизнес</option>
        <option class="translate" value="mall">Торговые центры</option>
    </select> 
    <div class="filter-icon" id="companyFilterIcon">
        <i class="fas fa-filter"></i>
    </div>
</div>

<div class="products-grid">
    <!-- Карточки будут добавляться динамически -->
</div>
