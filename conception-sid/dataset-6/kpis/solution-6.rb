require 'csv'
require 'pry'


TICKETS = CSV.parse(File.read("dataset-6-tickets-kpis.csv"), headers: true)

TICKETS_PER_QUARTER = TICKETS.group_by do |ticket|
  date = Date.parse(ticket["in_progress"])
  quarter = (date.month / 3.0).ceil
  "q#{quarter}".to_sym
end

MONTHS_PER_QUARTER = {
  q1: [1, 2, 3],
  q2: [4, 5, 6],
  q3: [7, 8, 9],
  q4: [10, 11, 12]
}

def business_days(month)
  (1..Date.new(2021, month, -1).day).to_a.reject do |day|
    Date.new(2021, month, day).saturday? || Date.new(2021, month, day).sunday?
  end.count
end


# 1. Dev capacity: the number of developers days in the team per quarter
def dev_capacity_per_quarter
  days_per_quarter = MONTHS_PER_QUARTER.inject({}) do |quarters, (quarter, months)|
    days_count = months.map do |month|
      business_days(month)
    end.reduce(:+)
    quarters[quarter] = days_count
    quarters
  end

  devs_per_quarter = TICKETS_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    quarters[quarter] = tickets.group_by { |ticket| ticket["dev"] }.keys.count
    quarters
  end

  values = devs_per_quarter.inject({}) do |quarters, (quarter, devs)|
    quarters[quarter] = devs * days_per_quarter[quarter]
    quarters
  end.to_a

  values.unshift(['quarter', 'dev_capacity'])
  File.write('dev_capacity.csv', values.map(&:to_csv).join)
end

dev_capacity_per_quarter

# 2. Total number of tickets done per quarter
def tickets_done_per_quarter
  values = TICKETS_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    quarters[quarter] = tickets.count
    quarters
  end.to_a

  values.unshift(['quarter', 'total_tickets'])
  File.write('tickets_done_per_quarter.csv', values.map(&:to_csv).join)
end

tickets_done_per_quarter
