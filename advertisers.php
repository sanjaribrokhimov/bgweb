<style>
    .product-info
    {
        height: 135px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>

<div class="filter-container">
    <select class="form-select category-select" id="companyCategorySelect">
        <option value="">Все категории</option>
        <option value="retail">Розничная торговля</option>
                            <option value="wholesale">Оптовая торговля</option>
                            <option value="services">Услуги и сервис</option>
                            <option value="manufacturing">Производство</option>
                            <option value="tech">IT и технологии</option>
                            <option value="finance">Финансы и банкинг</option>
                            <option value="construction">Строительство</option>
                            <option value="realestate">Недвижимость</option>
                            <option value="healthcare">Здравоохранение</option>
                            <option value="education">Образование</option>
                            <option value="hospitality">Гостиничный бизнес</option>
                            <option value="restaurant">Рестораны и общепит</option>
                            <option value="logistics">Логистика</option>
                            <option value="agriculture">Сельское хозяйство</option>
                            <option value="energy">Энергетика</option>
                            <option value="media">Медиа и развлечения</option>
                            <option value="consulting">Консалтинг</option>
                            <option value="automotive">Автомобильный бизнес</option>
                            <option value="mall">Торговые центры</option>
    </select>
    <div class="filter-icon" id="companyFilterIcon">
        <i class="fas fa-filter"></i>
    </div>
</div>

<div class="products-grid">
    <!-- Карточки будут добавляться динамически -->
</div>
<script src="scripts/companies.js?v=1.0.1"></script>
