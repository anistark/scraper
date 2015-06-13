import urllib2, csv
from bs4 import BeautifulSoup


def scrapepage(mylink):
	baselink = 'https://www.degustapanama.com/search'
	subaselink = 'https://www.degustapanama.com'
	req = urllib2.Request(url=mylink,data=b'None',headers={'User-Agent':' Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36'})
	handler = urllib2.urlopen(req)

	soup = BeautifulSoup(handler)

	rearticle = soup.find_all("a", {"class" : "url"})
	readdress = soup.find_all('address', {'class' : "adr"})

	final_dict = {}
	sub_dict = {}

	new_art = []
	new_add = []
	new_lat = []
	new_lng = []

	for y in readdress:
		new_add.append(y.text)

	for x in rearticle:
		if(x.text != 'Guia Degusta'):
			sublink = subaselink+x['href']

			reqsub = urllib2.Request(url=sublink,data=b'None',headers={'User-Agent':' Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36'})
			handlersub = urllib2.urlopen(reqsub)

			soupsub = BeautifulSoup(handlersub)

			reloc = soupsub.find("div", {"class" : "map"})
			if(reloc):
				new_lat.append(reloc['data-lat'])
				new_lng.append(reloc['data-lng'])
			else:
				new_lat.append('NA')
				new_lng.append('NA')

			soupsub.prettify()

		new_art.append(x.text)

	final_dict = dict(zip(new_art, new_add))
	for var1, var2 in final_dict.items():
		sub_dict = dict(zip(new_lat, new_lng))
		for var3, var4 in sub_dict.items():
			f.writerow([var1.encode('utf8'), var2.encode('utf8'), var3.encode('utf8'), var4.encode('utf8')])
			break
	
	nextlink = soup.find('a', {'class' : "next"})
	if(nextlink):
		nexturl = baselink+nextlink['href']
		scrapepage(nexturl)

	soup.prettify()


baselink = 'https://www.degustapanama.com/search'
pagelink = '?ciudad=panama&q=&pagina=1&orden=comida#!bnVsbA%3D%3D'
mylink = baselink+pagelink

# Write to CSV file
f = csv.writer(open("restaurants.csv", "w"))
f.writerow(["Restaurant Name", "Restaurant Address", "Restaurant Latitude", "Restaurant Longitude"])

scrapepage(mylink)

print "Finished Scrapping"
