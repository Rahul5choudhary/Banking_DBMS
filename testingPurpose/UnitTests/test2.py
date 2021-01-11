from selenium import webdriver
import unittest
import time
import os


class UnitTesting(unittest.TestCase):
    @classmethod 
    def setUpClass(cls):
        cls.driver = webdriver.Chrome('C:/Users/vaibh/OneDrive/Desktop/chromedriver.exe')
        cls.driver.implicitly_wait(10)
        cls.driver.maximize_window()

    def testClickLogin(self):
        self.driver.get("http://localhost/banking_dbms")
        self.driver.find_element_by_id("login").click()
        time.sleep(1)

    def testEnterUsername(self):
        import envVar
        envVar.setVar()
        username_input = "/html/body/div[2]/form/div[1]/input"
        username = os.getenv('EMAIL')
        username_field = self.driver.find_element_by_xpath(username_input)
        username_field.click()
        username_field.send_keys(username)
        time.sleep(1)
        login_path = "/html/body/div[2]/form/div[3]/button"
        login_field = self.driver.find_element_by_xpath(login_path)
        login_field.click()
        time.sleep(3)
    
    def testEnterPassword(self):
        import envVar
        envVar.setVar()
        password = os.getenv('PASSWORD')
        password_input = "/html/body/div[2]/form/div[2]/input"
        password_field = self.driver.find_element_by_xpath(password_input)
        password_field.click()
        password_field.send_keys(password)
        time.sleep(1)

    def testTransaction(self):
        self.driver.find_element_by_xpath('//*[@id="service"]/div/div[2]/div[1]/div/a/div/span/i').click()
        self.driver.implicitly_wait(10)
        self.driver.find_element_by_xpath('//*[@id="input"]').click()
        self.driver.find_element_by_xpath('//*[@id="input"]').send_keys(40)
        time.sleep(2)

    def testTransaction2(self):
        self.driver.find_element_by_name('amount').click()
        self.driver.find_element_by_name('amount').send_keys(1)
        time.sleep(2)

    def testTransaction3(self):
        self.driver.find_element_by_name('terms').click()
        time.sleep(2)

    def testTransaction4(self):
        self.driver.find_element_by_name('submit').click()
        time.sleep(3)


    @classmethod
    def tearDownClass(cls):
        cls.driver.close()
        cls.driver.quit()
        print("Test Completed")


if __name__ == '__main__':
    unittest.main()