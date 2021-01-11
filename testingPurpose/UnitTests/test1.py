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

    def testTransactionBalance(self):
        self.driver.find_element_by_xpath('//*[@id="service"]/div/div[2]/div[3]/div/a/div/span/i').click()
        time.sleep(3)

    @classmethod
    def tearDownClass(cls):
        cls.driver.close()
        cls.driver.quit()
        print("Test Completed")


if __name__ == '__main__':
    unittest.main()



