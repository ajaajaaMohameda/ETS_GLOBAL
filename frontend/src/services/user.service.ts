import { apiClient } from '@/lib/axios';
import { User } from '@/types';

export const userService = {
  async getCurrentUser(): Promise<User> {
    const response = await apiClient.get('/me');
    return response.data;
  },

  async updateProfile(name: string, email: string): Promise<User> {
    const response = await apiClient.put('/me', { name, email });
    return response.data;
  }
};