require 'csv'
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
