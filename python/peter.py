import csv

f1 = csv.reader(open('restaurants.csv', 'rb'))
writer = csv.writer(open("restaurants_sorted.csv", "wb"))
redupli = set()
for row in f1:
    if row[1] not in redupli:
        writer.writerow(row)
        redupli.add( row[1] )