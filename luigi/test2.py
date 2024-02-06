# test_matplotlib.py
import matplotlib.pyplot as plt

def test():
    plt.plot([1, 2, 3], [4, 5, 6])
    plt.title("Simple Plot")
    plt.xlabel("x-axis")
    plt.ylabel("y-axis")
    plt.savefig("test_plot.png")
    print("Matplotlib test successful - plot saved as test_plot.png")

if __name__ == '__main__':
    test()
