require 'csv'
require 'json'
require 'pry'

DELIMITERS = [' | ',' - ',' ; ']
rows = CSV.parse(File.read("dataset-5.csv"), headers: true)

file = File.read('dataset-5-hotel-prices.json')
HOTEL_PRICES = JSON.parse(file).map do |hotel_price|
  [hotel_price['hotel'], hotel_price['price_per_night']]
end.to_h

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
  result = {}
  rows.each do |row|
    country_name = row['location'].split(Regexp.union(DELIMITERS)).last
    country_slug = country_name.downcase.gsub(/\s/,'-')
    arrival_date = Date.strptime(row['arrival_date'], "%m-%d-%Y")
    departure_date = Date.parse(row['departure_date'])
    days_of_stay = (departure_date - arrival_date).to_i
    price = days_of_stay * HOTEL_PRICES[row['hotel']]
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

# 3. What are the 10 hotels with the most revenues during the summer ?
# (By summer, consider that either the arrival and/or the departure are in July or August)

def summer?(arrival_date, departure_date)
  arrival_date.month == 7 || arrival_date.month == 8 || departure_date.month == 7 || departure_date.month == 8
end

def hotels_revenues(rows)
  result = {}
  rows.each do |row|
    hotel = row['hotel']
    arrival_date = Date.strptime(row['arrival_date'], "%m-%d-%Y")
    departure_date = Date.parse(row['departure_date'])
    if summer?(arrival_date, departure_date)
      days_of_stay = (departure_date - arrival_date).to_i
      price = days_of_stay * HOTEL_PRICES[row['hotel']]
      if result.key?(hotel)
        result[hotel] = result[hotel] + price
      else
        result[hotel] = price
      end
    end
  end


  # Export to CSV
  filename = "solution-5/question-3/hotels-revenues.csv.csv"
  result = result.sort_by { |hotel, revenue| -revenue }.first(10)
  result.unshift(['hotel', 'revenue'])
  File.write(filename, result.map(&:to_csv).join)
end

hotels_revenues(rows)

# 4. Compare the transportation modes used by men and women in each country.

def genre(row)
  return 'm' if ['M', 'Masc', 'Male', 'masc'].include?(row['genre'])
  return 'f' if ['F', 'Fem', 'Female', 'fem'].include?(row['genre'])
  return '-'
end

def transportation_modes_by_genre(rows)
  # result =
  # {
  #   india: {
  #     m: { car: 2, plane: 3  }
  #     f: { bicycle: 5, taxi: 1 }
  #   }
  # }

  # {
  #   india:
  #   [
  #     ['M','car','132'],
  #     ['F','bicycle','5'],
  #     ['M','plane','3'],
  #     ['F','taxi','1']
  #   ]
  # }
  # result[:india][:m][:car] = result[:india][:m][:car] + 1

  result = {}
  rows.each do |row|
    country = row['location'].downcase.split(Regexp.union(DELIMITERS)).last.gsub(/\s/,'-')
    transportation_modes = row['transportation_modes'].downcase.split(Regexp.union(DELIMITERS))
    genre = genre(row)
    if result.key?(country)
      transportation_modes_of_country = result[country]
      transportation_modes.each do |mode|
        if transportation_modes_of_country.key?(genre)
          transportation_modes_for_genre = transportation_modes_of_country[genre]
          if transportation_modes_for_genre.key?(mode)
            transportation_modes_for_genre[mode] = transportation_modes_for_genre[mode] + 1
          else
            transportation_modes_for_genre[mode] = 1
          end
        else
          transportation_modes_of_country[genre] = { mode => 1 }
        end
      end
    else
      result[country] = { genre => transportation_modes.map { |mode | [mode, 1]}.to_h }
    end
  end
  result


  # Export to CSV
  result.each do |country, transportation_modes_by_genre|
    filename = "solution-5/question-4/transportation-modes-genre-#{country}.csv"
    values = []
    transportation_modes_by_genre.each do |genre, transportation_modes|
      transportation_modes.to_a.sort_by { |mode, count| mode }.map do |mode, count|
        values << [genre] + [mode, count]
      end
    end
    values.unshift(['genre', 'transportation_mode', 'count'])
    File.write(filename, values.map(&:to_csv).join)
  end
end

transportation_modes_by_genre(rows)


