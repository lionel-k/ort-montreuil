A partir du fichier dataset_4, j'extrais la liste des ingrédients pour lesquels il y a eu un contrat de signé.

J’enlève les doublons pour afficher les ingrédients qui sont en double.

C'est la liste des ingrédients autorisés que je suis autorisé à vendre aux hôtels prospects.
J'ai créé une table authorized_ingredients dans la base de données Leads.

Pour savoir si un hôtel a besoin d'un ingrédient, j’extrais la liste des ingrédients qui interviennent dans une recette pour un hôtel.
La jointure Hôtel – Recette – Ingrédient donnera la liste des ingrédients dont l'hôtel a besoin. 
Requête SQL :
select hotels.hotel_name, ingredients.ingredient_name from dishes, ingredients, hotels where  hotels.hotel_code = dishes.hotel_code and dishes.dish_code = ingredients.dish_code;


Cela ne sert à rien de chercher lui vendre un ingrédient qui n'intervient pas dans une de ses recettes, même si je suis autorisé à lui vendre (c’est-à-dire qu'il faut partie de la table des ingrédients autorisés).
Donc à partir du résultat précédent, je dois faire une jointure pour lier avec la table des ingrédients autorisés.
Cela donnera la liste des ingrédients par hôtel que je peux leur vendre et dont ils ont besoin.
select hotels.hotel_name, ingredients.ingredient_name from dishes, ingredients, hotels, authorized_ingredients where  hotels.hotel_code = dishes.hotel_code and dishes.dish_code = ingredients.dish_code and ingredients.ingredient_name = authorized_ingredients.ingredients ;
