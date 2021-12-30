require 'csv'
require 'pry'

def business_days(month)
  (1..Date.new(2021, month, -1).day).to_a.reject do |day|
    Date.new(2021, month, day).saturday? || Date.new(2021, month, day).sunday?
  end.count
end

tickets = CSV.parse(File.read("dataset-6-tickets-kpis.csv"))
binding.pry


# 1. Dev capacity: the number of developers days in the team per quarter

# def dev_capacity
#   per month
#     - how many dev
#     - how many business days
#     - dev * days
# end
