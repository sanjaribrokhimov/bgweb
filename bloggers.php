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
    <select class="form-select category-select" id="bloggerCategorySelect">
        <option class="translate" value="">Все категории</option>
        <option class="translate" value="lifestyle">Лайфстайл и влог</option>
        <option class="translate" value="fashion">Мода и стиль</option>
        <option class="translate" value="beauty">Бьюти и косметика</option>
        <option class="translate" value="travel">Путешествия и туризм</option>
        <option class="translate" value="food">Еда и кулинария</option>
        <option class="translate" value="sport">Спорт и фитнес</option>
        <option class="translate" value="business">Бизнес и предпринимательство</option>
        <option class="translate" value="education">Образование и саморазвитие</option>
        <option class="translate" value="technology">Технологии и гаджеты</option>
        <option class="translate" value="gaming">Игры и киберспорт</option>
        <option class="translate" value="music">Музыка и развлечения</option>
        <option class="translate" value="art">Искусство и творчество</option>
        <option class="translate" value="health">Здоровье и wellness</option>
        <option class="translate" value="parenthood">Родительство и семья</option>
        <option class="translate" value="pets">Домашние животные</option>
        <option class="translate" value="cars">Автомобили и транспорт</option>
        <option class="translate" value="finance">Финансы и инвестиции</option>
        <option class="translate" value="motivation">Мотивация и психология</option>
    </select>
    <div class="filter-icon" id="bloggerFilterIcon">
        <i class="fas fa-filter"></i>
    </div>
</div>

<div class="products-grid">
    <!-- Карточки будут добавляться динамически -->
</div>
<script src="scripts/bloggers.js?v=1.1.1"></script>

