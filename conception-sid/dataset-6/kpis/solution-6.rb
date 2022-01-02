#!/usr/bin/env ruby

require 'csv'
require 'pry'
require 'date'

TICKETS = CSV.parse(File.read("dataset-6-tickets-kpis.csv"), headers: true)

TICKETS_PER_QUARTER = TICKETS.group_by do |ticket|
  date = Date.parse(ticket["in_progress"])
  quarter = (date.month / 3.0).ceil
  "q#{quarter}".to_sym
end

TICKETS_DONE_PER_QUARTER = TICKETS.group_by do |ticket|
  date = Date.parse(ticket["done"])
  quarter = (date.month / 3.0).ceil
  "q#{quarter}".to_sym
end

TICKETS_DONE_PER_WEEK = TICKETS.group_by do |ticket|
  date = Date.parse(ticket["done"])
  week = date.strftime('%-V')
  "w#{week}".to_sym
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
  values = TICKETS_DONE_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    quarters[quarter] = tickets.count
    quarters
  end.to_a

  values.unshift(['quarter', 'total_tickets'])
  File.write('tickets_done_per_quarter.csv', values.map(&:to_csv).join)
end

tickets_done_per_quarter

# 3. Average number of tickets done per developer per week
# per week, how many tickets done
# per week, how many dev were present
# per week divide number of tickets by number of devs
# per quarter, divide sum of tickets done per weeks by number of weeks
def avg_tickets_per_dev_per_week
  tickets_per_quarter_per_week = TICKETS_DONE_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    tickets_per_week = tickets.group_by do |ticket|
      date = Date.parse(ticket["done"])
      week = date.strftime('%-V')
      "w#{week}".to_sym
    end
    quarters[quarter] = tickets_per_week
    quarters
  end

  values = tickets_per_quarter_per_week.inject({}) do |quarters, (quarter, tickets_per_week)|
    avg_tickets_per_week = tickets_per_week.inject({}) do |weeks, (week, tickets)|
      weeks[week] = tickets.count / tickets.map { |ticket| ticket["dev"] }.uniq.count
      weeks
    end
    quarters[quarter] = avg_tickets_per_week.values.reduce(:+).to_f / avg_tickets_per_week.values.count
    quarters
  end.to_a

  values.unshift(['quarter', 'average_tickets'])
  File.write('avg_tickets_done_per_dev_per_week.csv', values.map(&:to_csv).join)
end

avg_tickets_per_dev_per_week


# 4. Median cycle time of tickets per quarter
# per ticket, how many days it took to complete
# sum of days / number of tickets
def median_cycle_time_per_quarter
  values = TICKETS_DONE_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    days_per_ticket = tickets.map do |ticket|
      (Date.parse(ticket["done"]) - Date.parse(ticket["in_progress"])).to_i
    end
    quarters[quarter] = (days_per_ticket.reduce(:+).to_f / days_per_ticket.count).round(2)
    quarters
  end.to_a

  values.unshift(['quarter', 'median_cycle_time'])
  File.write('median_cycle_time_per_quarter.csv', values.map(&:to_csv).join)
end

median_cycle_time_per_quarter

# 5. Median cycle time of tickets per quarter, by type
def median_cycle_time_per_quarter_by_type
  median_cycle_per_quarter = TICKETS_DONE_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    tickets_by_type = tickets.group_by { |ticket| ticket["type"] }

    median_cycle_time_per_type = tickets_by_type.inject({}) do |types, (type, tickets)|
      days_per_ticket = tickets.map do |ticket|
        (Date.parse(ticket["done"]) - Date.parse(ticket["in_progress"])).to_i
      end
      types[type] = (days_per_ticket.reduce(:+).to_f / days_per_ticket.count).round(2)
      types
    end
    quarters[quarter] = median_cycle_time_per_type.to_a.sort_by { |type, median_cycle| type }
    quarters
  end

  values = []
  median_cycle_per_quarter.each do |quarter, median_cycle_time_per_type|
    median_cycle_time_per_type.map do |type, median_cycle_time|
      values << [quarter, type, median_cycle_time]
    end
  end

  values.unshift(['quarter', 'type', 'median_cycle_time'])
  File.write('median_cycle_time_per_quarter_by_type.csv', values.map(&:to_csv).join)
end

median_cycle_time_per_quarter_by_type

# 6. Time Allocation (in percentage) by Ticket Type per quarter
# how many tickets by type per quarter
# which percentage each type represents
def time_allocation_by_type_per_quarter
  time_allocation_per_quarter = TICKETS_DONE_PER_QUARTER.inject({}) do |quarters, (quarter, tickets)|
    tickets_by_type = tickets.group_by { |ticket| ticket["type"] }

    time_allocation_per_type = tickets_by_type.inject({}) do |types, (type, tickets)|
      types[type] = ((tickets.count.to_f / TICKETS_DONE_PER_QUARTER[quarter].count) * 100).round(2)
      types
    end
    quarters[quarter] = time_allocation_per_type.to_a.sort_by { |type, time_allocation| type }
    quarters
  end

  values = []
  time_allocation_per_quarter.each do |quarter, time_allocation_per_type|
    time_allocation_per_type.map do |type, time_allocation|
      values << [quarter, type, time_allocation]
    end
  end

  values.unshift(['quarter', 'type', 'time_allocation'])
  File.write('time_allocation_by_type_per_quarter.csv', values.map(&:to_csv).join)
end

time_allocation_by_type_per_quarter
