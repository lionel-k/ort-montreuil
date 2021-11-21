SELECT DISTINCT i.ingredient_name, GROUP_CONCAT(h.hotel_name) as lstHotels FROM ingredients i, dishes d, hotels h WHERE h.hotel_code = d.hotel_code AND d.dish_code = i.dish_code 
GROUP BY i.ingredient_name
