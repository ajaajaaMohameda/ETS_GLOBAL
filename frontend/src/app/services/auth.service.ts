import { apiClient } from '@/lib/axios';
import Cookies from 'js-cookie';

export const authService = {
  async login(email: string, password: string) {
    const response = await apiClient.post('/login_check', { email, password });
    
    if (response.data.token) {
      Cookies.set('jwt_token', response.data.token, { expires: 1, secure: true });
    }
    
    return response.data;
  },

  async register(name: string, email: string, password: string) {
    const response = await apiClient.post('/register', { name, email, password });
    return response.data;
  },

  logout() {
    Cookies.remove('jwt_token');
  },
  
  isAuthenticated(): boolean {
    return !!Cookies.get('jwt_token');
  }
};