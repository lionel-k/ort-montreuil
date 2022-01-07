import json
from datetime import datetime
import locale
from collections import Counter
import csv
from os import write
locale.getlocale()
('fr_FR', 'UTF-8')


print("Nettoyage de données ")


with open('DATAS/dataset-5-json.json') as mon_fichier:
    data = json.load(mon_fichier)

#print(data)


### genre
for line in data:
    genre = line['genre']
    if(genre == 'F' or genre == 'Fem' or genre == 'fem' or genre =='Femme' or genre =='Female'):
        line['genre'] = 'Femme'
    if line['genre'] != 'Femme':
        line['genre'] = 'Homme'
        
    #print(line['genre'])

    #Date
    my_date_arrival = datetime.strptime(line['arrival_date'], '%m-%d-%Y')
    my_date_departure = datetime.strptime(line['departure_date'], '%d-%b-%Y')
    line['arrival_date'] = my_date_arrival.strftime('%d %b %Y')
    line['departure_date'] =  my_date_departure.strftime('%d %b %Y')


    #Location
    line['location'] = line['location'].replace(' ; ', ', ')
    line['location'] = line['location'].replace(' | ', ', ')
    line['location'] = line['location'].replace(' - ', ', ')
    
    #print(line['location'])

    #Transportation modes

    line['transportation_modes'] = line['transportation_modes'].replace(';', '-')
    line['transportation_modes'] = line['transportation_modes'].replace('|', '-')
    #print(line['transportation_modes'])

    line['hotel'] = line['hotel'].replace('-', ', ')
    #print(line['hotel'])



####### [1] - 

location_transportation_mode = []
countries = []
locations = []
for line in data:
    location = line['location']
    transports = []
    if location not in locations :
        for line2 in data:
            if(location == line2['location']):
                transports += line2['transportation_modes'].split(' - ')
        mode_dic = {}
        for t in transports:
            if t not in mode_dic:
                mode_dic[t] = 1
            else:
                mode_dic[t] += 1
        locations.append(location)
        #print(location + ' : ')
        #print(mode_dic)
        d = dict()
        d[location] = mode_dic
        location_transportation_mode.append(d)
#print(location_transportation_mode)


C = []
toDel = []

    
for l in location_transportation_mode:
    location = list(l.keys())[0]
    country = location
    country = country.split(', ')
    country = country[1]

    file = open('transportation-modes-'+country + '.csv', 'w+')
    writer = csv.writer(file)
    writer.writerow(['transportation', 'mode'])

    if country not in C :
        for l2 in location_transportation_mode :
            location2 = list(l2.keys())[0]
            country2 = location2
            country2 = country2.split(', ')
            country2 = country2[1]
            if(country == country2) and  (l != l2):
                #print(l)
                #print(l2)
                z = dict(Counter(l[location]) + Counter(l2[location2]))
                #print(z)
                l[location] = z
                print(z)
                for key, items in l[location].items():
                    print([key, items])
              
        #print(l[location])
        for key, items in l[location].items():
            writer.writerow([key, items])

        C.append(country)


    
#print(location_transportation_mode)


#### Dates



    
