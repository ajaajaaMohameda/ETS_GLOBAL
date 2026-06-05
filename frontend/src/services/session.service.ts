import { apiClient } from '@/lib/axios';
import { PaginatedResponse, Session } from '@/types';

export const sessionService = {
  async getSessions(page: number = 1, limit: number = 10): Promise<PaginatedResponse<Session>> {
    const response = await apiClient.get('/sessions', {
      params: { page, limit }
    });
    return response.data;
  }
};