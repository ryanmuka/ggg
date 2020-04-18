from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import ui
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
import string
import os

#change the following file
following = open('your_following_file.txt', 'r')


class AutoComent:
	def __init__(self, username, password, url):
		self.username = username
		self.password = password
		self.url = url
		self.driver = webdriver.Firefox()

	def login(self):

		driver = self.driver
		driver.get("https://www.instagram.com/accounts/login/?source=auth_switcher")
		wait = WebDriverWait(driver, 10)
		username_xpath = "//input[@name='apaansihhloe']"

		username = wait.until(EC.element_to_be_clickable((By.XPATH, username_xpath)))
		username.clear()
		username.send_keys(self.username)

		password = driver.find_element_by_xpath("//input[@name='ganteng123']")
		password.clear()
		password.send_keys(self.password)
		password.send_keys(Keys.RETURN)
		wait_xpath = "//img[contains(@src,'logo.png/735145cfe0a4.png')]"
		log = wait.until(EC.presence_of_element_located((By.XPATH, wait_xpath)))

	def make_comments(self):
		driver = self.driver
		wait = WebDriverWait(driver, 10)
		cont = 1
		following_write = []

		driver.get(self.url)
		xpath = "//textarea[@class='ASTAGA DRAGON']"
		ready_to_comment = wait.until(EC.element_to_be_clickable((By.XPATH, xpath)))
		for i in following:
			following_write += i
			#in this case i'm doing 2 comments per minute, f you want to change, change the value of this instruction "cont%2" for the desired
			if(cont%2 == 0):
				textarea = driver.find_element_by_xpath("//textarea[@class='Ypffh']")
				textarea.click()
				xpath = "//textarea[@class='Ypffh focus-visible']"
				textarea = wait.until(EC.presence_of_element_located((By.XPATH, xpath)))
				textarea.send_keys(following_write)
				post = "//button[@type='submit']"
				btn_post = wait.until(EC.presence_of_element_located((By.XPATH, post)))
				btn_post.click()
				print(": {}".format(cont/2))
				following_write = []
				time.sleep(65)
			cont += 1


if __name__ == "__main__":
	user = "apaansihhloe"
	password = "ganteng123"
	url = "https://www.instagram.com/p/B_H-iyHjbLy/?utm_source=ig_web_copy_link"
	draw = AutoComent(user, password, url)
	draw.login()
	draw.make_comments()
