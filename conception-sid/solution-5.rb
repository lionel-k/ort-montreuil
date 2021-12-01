require 'csv'
require 'pry'

DELIMITERS = [' | ',' - ',' ; ']
rows = CSV.parse(File.read("dataset-5.csv"), headers: true)

# 1. What are the transportation modes used per country ?
def transportation_modes_per_country(rows)
  result = {}
  rows.first(12).each do |row|
    country = row['location'].downcase.split(Regexp.union(DELIMITERS)).last
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
  result
  binding.pry
end

transportation_modes_per_country(rows)
