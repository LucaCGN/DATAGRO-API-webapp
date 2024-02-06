# test_pygame.py
import pygame

def test():
    pygame.init()
    size = (700, 500)
    screen = pygame.display.set_mode(size)
    pygame.display.set_caption("Test Pygame")
    done = False
    clock = pygame.time.Clock()
    while not done:
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                done = True
        screen.fill((255, 255, 255))
        pygame.display.flip()
        clock.tick(60)
    pygame.quit()
    print("Pygame test successful - window opened and closed")

if __name__ == '__main__':
    test()
