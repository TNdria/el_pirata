// Composable pour gérer le chargement optimisé des styles
export const useStyleLoader = () => {
  const loadCriticalStyles = () => {
    // S'assurer que les styles critiques sont chargés immédiatement
    if (process.client) {
      const style = document.createElement('style');
      style.textContent = `
        .login-container {
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #111111;
          padding: 48px 16px;
        }
        .login-card {
          max-width: 400px;
          width: 100%;
          background-color: #111111;
          padding: 32px;
          border-radius: 12px;
          border: 1px solid #2b2b2b;
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .login-title {
          font-size: 28px;
          font-weight: bold;
          color: white;
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
          margin: 0;
          text-align: center;
        }
        .form-input {
          width: 100%;
          padding: 12px 16px;
          border-radius: 8px;
          border: 1px solid #374151;
          background-color: #171e26;
          color: #d1d5db;
          font-size: 16px;
          transition: all 0.2s ease;
          box-sizing: border-box;
        }
        .login-button {
          width: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 12px 16px;
          background: linear-gradient(to bottom, #383f4d, #2F3542);
          color: white;
          border: none;
          border-radius: 8px;
          font-weight: 500;
          font-size: 16px;
          cursor: pointer;
          transition: all 0.3s ease;
          margin-top: 8px;
        }
      `;
      document.head.appendChild(style);
    }
  };

  const preloadStyles = () => {
    if (process.client) {
      // Précharger les styles non critiques
      const link = document.createElement('link');
      link.rel = 'preload';
      link.href = '/_nuxt/login.css';
      link.as = 'style';
      link.onload = () => {
        link.rel = 'stylesheet';
      };
      document.head.appendChild(link);
    }
  };

  return {
    loadCriticalStyles,
    preloadStyles
  };
};
