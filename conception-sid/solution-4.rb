require 'csv'
require 'pry'

table = CSV.parse(File.read("dataset-all-4.csv"), headers: true)

contract_ingredients = [['contract_id', 'hotel_name','ingredient_name']]
sold_hotels_ingredients = []
ingredients_with_licence = []

table.each do |row|
  contract_id = row["contract_id"]
  hotel_name = row["hotel_name"]
  ingredients = row["ingredients"].split(',')
  ingredients.each do |ingredient|
    contract_ingredients << [contract_id, hotel_name, ingredient]
    ingredients_with_licence << ingredient
    sold_hotels_ingredients << [hotel_name, ingredient]
  end
end
File.write("contract_ingredients.csv", contract_ingredients.map(&:to_csv).join)

ingredients_with_licence.uniq!
actual_leads = []
potential_leads = CSV.parse(File.read("potential-leads.csv"), headers: true)
potential_leads.each do |lead|
  lead_hotel_name = lead["hotel_name"]
  lead_ingredient_name = lead["ingredient_name"]
  if ingredients_with_licence.include?(lead_ingredient_name) && !sold_hotels_ingredients.include?([lead_hotel_name, lead_ingredient_name])
    actual_leads << [lead_hotel_name, lead_ingredient_name]
  end
end

actual_leads.sort_by! { |hotel_name, ingredient_name| [hotel_name, ingredient_name]  }
actual_leads.unshift(['hotel_name', 'ingredient_name'])
File.write("result.csv", actual_leads.map(&:to_csv).join)

# select count (*) from (
# SELECT distinct hotel_name, ingredient_name from hotels
# INNER JOIN dishes on hotels.hotel_code = dishes.hotel_code
# INNER JOIN ingredients on ingredients.dish_code = dishes.dish_code
# where ingredient_name in (select distinct ingredient_name from contract_ingredients)
# and (hotel_name, ingredient_name) not in (select hotel_name, ingredient_name from contract_ingredients)
# order by hotel_name, ingredient_name);
# 1574 leads



