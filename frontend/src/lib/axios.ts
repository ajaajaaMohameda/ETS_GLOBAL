// frontend/src/lib/axios.ts

import axios from 'axios';
import Cookies from 'js-cookie';

export const apiClient = axios.create({
  // On utilise la variable d'environnement définie dans docker-compose.yml
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// L'intercepteur intercepte la requête juste avant son envoi
apiClient.interceptors.request.use((config) => {
  const token = Cookies.get('jwt_token');
  
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  
  return config;
});