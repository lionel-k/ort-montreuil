require 'csv'
require 'pry'

teams = CSV.parse(File.read("teams.csv"), headers: true)
binding.pry
