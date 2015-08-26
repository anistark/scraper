# -*- coding: utf-8 -*-
import scrapy

from scrapy.contrib.spiders.init import InitSpider
from scrapy.http import Request, FormRequest
from scrapy.contrib.linkextractors.sgml import SgmlLinkExtractor
from scrapy.contrib.spiders import Rule
from scrapy.contrib.spiders import CrawlSpider, Rule

from scrapy.spider import BaseSpider
from scrapy.selector import HtmlXPathSelector


class LinkedinSpider(scrapy.Spider):
	name = "linkedin"
	allowed_domains = ["linkedin.com"]
	login_page = "https://www.linkedin.com/uas/login"
	start_urls = ["http://www.linkedin.com/csearch/results"]

	def start_requests(self):
		yield Request(
			url=self.login_page,
			callback=self.login,
			dont_filter=True)

	def login(self, response):
		return FormRequest.from_response(response,
			formdata={'session_key': 'myemail@gmail.com', 'session_password': 'mypassword'},
			callback=self.check_login_response)

	def check_login_response(self, response):
		#"""Check the response returned by a login request to see if we aresuccessfully logged in."""
		if "Sign Out" in response.body:
			self.log("\n\n\nSuccessfully logged in. Let's start crawling!\n\n\n")
			# Now the crawling can begin..
			return Request(url='http://linkedin.com/page/containing/links')
		else:
			self.log("\n\n\nFailed, Bad times :(\n\n\n")

	def parse_item(self, response):
		self.log("\n\n\n We got data! \n\n\n")
		self.log('Hi, this is an item page! %s' % response.url)
		hxs = HtmlXPathSelector(response)
		sites = hxs.select('//ol[@id=\'result-set\']/li')
		items = []
		for site in sites:
			item = LinkedconvItem()
			item['title'] = site.select('h2/a/text()').extract()
			item['link'] = site.select('h2/a/@href').extract()
			items.append(item)
		return items    

	# def parse(self, response):
	# 	pass
