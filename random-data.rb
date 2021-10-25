require 'securerandom'

def good
  ["EDAB0E", "D74CC7"].sample
end

def bad
  SecureRandom.hex(3).upcase
end

def sku
  rand(1..100) > 12 ? good : bad
end

def skus(number)
  1000.times do
   puts sku
  end
end

def units
  1000.times do
   puts rand(50..500)
  end
end

def products
end

units
# skus
