require 'csv'
require 'json'
require 'pry'

DELIMITERS = [' | ',' - ',' ; ']
rows = CSV.parse(File.read("dataset-5.csv"), headers: true)

# 1. What are the transportation modes used per country ?
def transportation_modes_per_country(rows)
  result = {}
  rows.each do |row|
    country = row['location'].downcase.split(Regexp.union(DELIMITERS)).last.gsub(/\s/,'-')
    transportation_modes = row['transportation_modes'].downcase.split(Regexp.union(DELIMITERS))
    if result.key?(country)
      transportation_modes_of_country = result[country]
      transportation_modes.each do |mode|
        if transportation_modes_of_country.include?(mode)
          transportation_modes_of_country[mode] = transportation_modes_of_country[mode] + 1
        else
          transportation_modes_of_country[mode] = 1
        end
      end
    else
      result[country] = transportation_modes.map { |mode | [mode, 1]}.to_h
    end
  end

  # Export to CSV
  result.each do |country, transportation_modes|
    filename = "solution-5/question-1/transportation-modes-#{country}.csv"
    values = transportation_modes.to_a.sort_by { |mode, count| mode }
    values.unshift(['transportation_mode', 'count'])
    File.write(filename, values.map(&:to_csv).join)
  end
end

transportation_modes_per_country(rows)

# 2. How much people paid for hotel per country ?
def countries_revenues(rows)
  file = File.read('dataset-5-hotel-prices.json')
  hotel_prices = JSON.parse(file).map do |hotel_price|
    [hotel_price['hotel'], hotel_price['price_per_night']]
  end.to_h
  result = {}
  rows.each do |row|
    country_name = row['location'].split(Regexp.union(DELIMITERS)).last
    country_slug = country_name.downcase.gsub(/\s/,'-')
    arrival_date = Date.strptime(row['arrival_date'], "%m-%d-%Y")
    departure_date = Date.parse(row['departure_date'])
    days_of_stay = (departure_date - arrival_date).to_i
    price = days_of_stay * hotel_prices[row['hotel']]
    if result.key?(country_slug)
      result[country_slug][:price] = result[country_slug][:price] + price
    else
      result[country_slug] = { country_name: country_name, price: price }
    end
  end

  # Export to CSV
  filename = "solution-5/question-2/countries-revenues.csv"
  result = result.sort_by { |country, data| -data[:price] }.map { |country, data| [data[:country_name], data[:price]] }
  result.unshift(['country', 'hotel_revenue'])
  File.write(filename, result.map(&:to_csv).join)
end

countries_revenues(rows)
