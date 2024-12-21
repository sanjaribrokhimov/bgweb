package handlers

import (
	"bloger_agencyBackend/database"
	"bloger_agencyBackend/models"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type UnifiedAd struct {
	Type      string      `json:"type"` // "blogger", "company", "freelancer"
	Data      interface{} `json:"data"`
	CreatedAt string      `json:"created_at"`
}

func GetAllAds(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	limit, _ := strconv.Atoi(c.DefaultQuery("limit", "10"))
	offset := (page - 1) * limit
	sort := c.DefaultQuery("sort", "created_at") // new, popular

	var unifiedAds []UnifiedAd

	// Получаем объявления от блогеров
	var bloggers []models.PostBlogger
	query := database.DB.Where("status = ?", "active")
	if sort == "popular" {
		query = query.Order("followers DESC")
	} else {
		query = query.Order("created_at DESC")
	}
	query.Offset(offset).Limit(limit).Find(&bloggers)

	for _, b := range bloggers {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "blogger",
			Data: gin.H{
				"id":             b.ID,
				"user_id":        b.UserID,
				"nickname":       b.Nickname,
				"category":       b.Category,
				"photo_base64":   b.PhotoBase64,
				"ad_comment":     b.AdComment,
				"instagram_link": b.InstagramLink,
				"telegram_link":  b.TelegramLink,
				"youtube_link":   b.YoutubeLink,
				"created_at":     b.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: b.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	// Получаем объявления от компаний
	var companies []models.Company
	database.DB.Offset(offset).Limit(limit).Find(&companies)

	for _, comp := range companies {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "company",
			Data: gin.H{
				"id":             comp.ID,
				"user_id":        comp.UserID,
				"name":           comp.Name,
				"category":       comp.Category,
				"photo_base64":   comp.PhotoBase64,
				"budget":         comp.Budget,
				"ad_comment":     comp.AdComment,
				"website_link":   comp.WebsiteLink,
				"instagram_link": comp.InstagramLink,
				"telegram_link":  comp.TelegramLink,
				"created_at":     comp.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: comp.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	// Получаем объявления от фрилансеров
	var freelancers []models.Freelancer
	database.DB.Offset(offset).Limit(limit).Find(&freelancers)

	for _, f := range freelancers {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "freelancer",
			Data: gin.H{
				"id":             f.ID,
				"user_id":        f.UserID,
				"name":           f.Name,
				"category":       f.Category,
				"photo_base64":   f.PhotoBase64,
				"ad_comment":     f.AdComment,
				"github_link":    f.GithubLink,
				"portfolio_link": f.PortfolioLink,
				"instagram_link": f.InstagramLink,
				"telegram_link":  f.TelegramLink,
				"youtube_link":   f.YoutubeLink,
				"created_at":     f.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: f.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	c.JSON(http.StatusOK, gin.H{
		"data":  unifiedAds,
		"page":  page,
		"limit": limit,
		"total": len(unifiedAds),
	})
}

func GetAdsByCategory(c *gin.Context) {
	category := c.Param("category")
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	limit, _ := strconv.Atoi(c.DefaultQuery("limit", "10"))
	offset := (page - 1) * limit

	// Проверяем валидность категории
	validCategories := map[string]bool{
		"blogger":    true,
		"company":    true,
		"freelancer": true,
	}

	if !validCategories[category] {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid category"})
		return
	}

	var result interface{}

	switch category {
	case "blogger":
		var ads []models.PostBlogger
		database.DB.Order("RANDOM()").Offset(offset).Limit(limit).Find(&ads)
		result = ads
	case "company":
		var ads []models.Company
		database.DB.Order("RANDOM()").Offset(offset).Limit(limit).Find(&ads)
		result = ads
	case "freelancer":
		var ads []models.Freelancer
		database.DB.Order("RANDOM()").Offset(offset).Limit(limit).Find(&ads)
		result = ads
	}

	c.JSON(http.StatusOK, gin.H{
		"data":  result,
		"page":  page,
		"limit": limit,
	})
}

func GetAdDetails(c *gin.Context) {
	id := c.Param("id")
	adType := c.Param("type")

	switch adType {
	case "blogger":
		var post models.PostBlogger
		if err := database.DB.First(&post, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Blogger not found"})
			return
		}

		c.JSON(http.StatusOK, gin.H{
			"id":             post.ID,
			"user_id":        post.UserID,
			"nickname":       post.Nickname,
			"category":       post.Category,
			"photo_base64":   post.PhotoBase64,
			"ad_comment":     post.AdComment,
			"instagram_link": post.InstagramLink,
			"telegram_link":  post.TelegramLink,
			"youtube_link":   post.YoutubeLink,
			"created_at":     post.CreatedAt.Format("2006-01-02 15:04:05"),
		})

	case "company":
		var company models.Company
		if err := database.DB.First(&company, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Company not found"})
			return
		}

		c.JSON(http.StatusOK, gin.H{
			"id":             company.ID,
			"user_id":        company.UserID,
			"name":           company.Name,
			"category":       company.Category,
			"photo_base64":   company.PhotoBase64,
			"budget":         company.Budget,
			"ad_comment":     company.AdComment,
			"website_link":   company.WebsiteLink,
			"instagram_link": company.InstagramLink,
			"telegram_link":  company.TelegramLink,
			"created_at":     company.CreatedAt.Format("2006-01-02 15:04:05"),
		})

	case "freelancer":
		var freelancer models.Freelancer
		if err := database.DB.First(&freelancer, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Freelancer not found"})
			return
		}

		c.JSON(http.StatusOK, gin.H{
			"id":             freelancer.ID,
			"user_id":        freelancer.UserID,
			"name":           freelancer.Name,
			"category":       freelancer.Category,
			"photo_base64":   freelancer.PhotoBase64,
			"ad_comment":     freelancer.AdComment,
			"github_link":    freelancer.GithubLink,
			"portfolio_link": freelancer.PortfolioLink,
			"instagram_link": freelancer.InstagramLink,
			"telegram_link":  freelancer.TelegramLink,
			"youtube_link":   freelancer.YoutubeLink,
			"created_at":     freelancer.CreatedAt.Format("2006-01-02 15:04:05"),
		})

	default:
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid ad type"})
		return
	}
}

func DeleteAd(c *gin.Context) {
	adType := c.Param("type")
	id := c.Param("id")

	switch adType {
	case "blogger":
		var post models.PostBlogger
		if err := database.DB.First(&post, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Объявление блогера не найдено"})
			return
		}
		if err := database.DB.Delete(&post).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при удалении объявления блогера"})
			return
		}

	case "company":
		var company models.Company
		if err := database.DB.First(&company, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Объявление компании не найдено"})
			return
		}
		if err := database.DB.Delete(&company).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при удалении объявления компании"})
			return
		}

	case "freelancer":
		var freelancer models.Freelancer
		if err := database.DB.First(&freelancer, id).Error; err != nil {
			c.JSON(http.StatusNotFound, gin.H{"error": "Объявление фрилансера не найдено"})
			return
		}
		if err := database.DB.Delete(&freelancer).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при удалении объявления фрилансера"})
			return
		}

	default:
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверный тип объявления"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Объявление успешно удалено"})
}

func GetUserAdsByCategory(c *gin.Context) {
	category := c.Param("category")
	userID := c.Param("user_id")

	switch category {
	case "blogger":
		var posts []models.PostBlogger
		if err := database.DB.Where("user_id = ?", userID).Find(&posts).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при получении объявлений блогера"})
			return
		}
		c.JSON(http.StatusOK, posts)

	case "company":
		var companies []models.Company
		if err := database.DB.Where("user_id = ?", userID).Find(&companies).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при получении объявлений компании"})
			return
		}
		c.JSON(http.StatusOK, companies)

	case "freelancer":
		var freelancers []models.Freelancer
		if err := database.DB.Where("user_id = ?", userID).Find(&freelancers).Error; err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Ошибка при получении объявлений фрилансера"})
			return
		}
		c.JSON(http.StatusOK, freelancers)

	default:
		c.JSON(http.StatusBadRequest, gin.H{"error": "Неверная категория"})
		return
	}
}

func SearchAds(c *gin.Context) {
	query := c.Query("q")
	category := c.Query("category")

	var unifiedAds []UnifiedAd

	// Поиск среди объявлений блогеров
	var bloggers []models.PostBlogger
	bloggerQuery := database.DB.Where("status = ?", "active")
	if query != "" {
		searchQuery := "%" + query + "%"
		bloggerQuery = bloggerQuery.Where(
			"LOWER(nickname) LIKE LOWER(?) OR LOWER(category) LIKE LOWER(?) OR LOWER(ad_comment) LIKE LOWER(?)",
			searchQuery, searchQuery, searchQuery,
		)
	}
	if category != "" {
		bloggerQuery = bloggerQuery.Where("LOWER(category) = LOWER(?)", category)
	}
	bloggerQuery.Find(&bloggers)

	for _, b := range bloggers {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "blogger",
			Data: gin.H{
				"id":             b.ID,
				"user_id":        b.UserID,
				"nickname":       b.Nickname,
				"category":       b.Category,
				"photo_base64":   b.PhotoBase64,
				"ad_comment":     b.AdComment,
				"instagram_link": b.InstagramLink,
				"telegram_link":  b.TelegramLink,
				"youtube_link":   b.YoutubeLink,
				"created_at":     b.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: b.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	// Поиск среди компаний
	var companies []models.Company
	companyQuery := database.DB.Where("status = ?", "active")
	if query != "" {
		searchQuery := "%" + query + "%"
		companyQuery = companyQuery.Where(
			"LOWER(name) LIKE LOWER(?) OR LOWER(category) LIKE LOWER(?) OR LOWER(ad_comment) LIKE LOWER(?)",
			searchQuery, searchQuery, searchQuery,
		)
	}
	if category != "" {
		companyQuery = companyQuery.Where("LOWER(category) = LOWER(?)", category)
	}
	companyQuery.Find(&companies)

	for _, comp := range companies {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "company",
			Data: gin.H{
				"id":             comp.ID,
				"user_id":        comp.UserID,
				"name":           comp.Name,
				"category":       comp.Category,
				"photo_base64":   comp.PhotoBase64,
				"budget":         comp.Budget,
				"ad_comment":     comp.AdComment,
				"website_link":   comp.WebsiteLink,
				"instagram_link": comp.InstagramLink,
				"telegram_link":  comp.TelegramLink,
				"created_at":     comp.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: comp.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	// Поиск среди фрилансеров
	var freelancers []models.Freelancer
	freelancerQuery := database.DB.Where("status = ?", "active")
	if query != "" {
		searchQuery := "%" + query + "%"
		freelancerQuery = freelancerQuery.Where(
			"LOWER(name) LIKE LOWER(?) OR LOWER(category) LIKE LOWER(?) OR LOWER(ad_comment) LIKE LOWER(?)",
			searchQuery, searchQuery, searchQuery,
		)
	}
	if category != "" {
		freelancerQuery = freelancerQuery.Where("LOWER(category) = LOWER(?)", category)
	}
	freelancerQuery.Find(&freelancers)

	for _, f := range freelancers {
		unifiedAds = append(unifiedAds, UnifiedAd{
			Type: "freelancer",
			Data: gin.H{
				"id":             f.ID,
				"user_id":        f.UserID,
				"name":           f.Name,
				"category":       f.Category,
				"photo_base64":   f.PhotoBase64,
				"ad_comment":     f.AdComment,
				"github_link":    f.GithubLink,
				"portfolio_link": f.PortfolioLink,
				"instagram_link": f.InstagramLink,
				"telegram_link":  f.TelegramLink,
				"youtube_link":   f.YoutubeLink,
				"created_at":     f.CreatedAt.Format("2006-01-02 15:04:05"),
			},
			CreatedAt: f.CreatedAt.Format("2006-01-02 15:04:05"),
		})
	}

	// Группируем результаты по категориям
	groupedResults := gin.H{
		"bloggers":    []UnifiedAd{},
		"companies":   []UnifiedAd{},
		"freelancers": []UnifiedAd{},
	}

	for _, ad := range unifiedAds {
		switch ad.Type {
		case "blogger":
			groupedResults["bloggers"] = append(groupedResults["bloggers"].([]UnifiedAd), ad)
		case "company":
			groupedResults["companies"] = append(groupedResults["companies"].([]UnifiedAd), ad)
		case "freelancer":
			groupedResults["freelancers"] = append(groupedResults["freelancers"].([]UnifiedAd), ad)
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"results":  groupedResults,
		"total":    len(unifiedAds),
		"query":    query,
		"category": category,
	})
}
