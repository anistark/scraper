import json
from pprint import pprint
import unicodedata
import urllib2
import csv

with open('kimonoData.json') as data_file:  
	data = json.load(data_file)
	results = data['results']
	collections = results['collection1']
	final_result = []
	base_url = 'http://engine-codes.herokuapp.com/api/troublecode?troublecode='
	f = csv.writer(open("enginedata.csv", "w"))
	f.writerow(["codeId", "text", "urldata"])
	for item in collections:
		links = item['links']
		text = links['text']
		textNormalised = unicodedata.normalize('NFKD', text).encode('ascii','ignore')
		codeId = textNormalised.partition(' ')[0]
		url = base_url + codeId
		try:
			response = urllib2.urlopen(url)
			html = response.read()
		except:
			html = []
		end_result = {}
		end_result['text'] = textNormalised
		end_result['codeId'] = codeId
		end_result['urldata'] = html
		f.writerow([codeId, textNormalised, html])
		final_result.append(end_result)
		pprint('done')


pprint(final_result)
