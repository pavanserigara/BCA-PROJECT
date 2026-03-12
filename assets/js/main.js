/**
 * VidyaSetu – Main Javascript
 * Author: Antigravity AI
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // Smooth transitions for cards
    const cards = document.querySelectorAll('.card-hover');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
             card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
             card.style.transform = 'translateY(0)';
        });
    });

    // Handle standard alerts
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

});
