# test_beautifulsoup.py
from bs4 import BeautifulSoup

def test():
    html_doc = "<html><head><title>The Dormouse's story</title></head>"
    soup = BeautifulSoup(html_doc, 'html.parser')
    print(soup.title.string)

if __name__ == '__main__':
    test()
