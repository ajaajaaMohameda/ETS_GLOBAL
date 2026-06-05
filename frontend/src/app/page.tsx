'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { authService } from '@/services/auth.service';

export default function HomePage() {
  const router = useRouter();

  useEffect(() => {
    if (authService.isAuthenticated()) {
      router.push('/reservations');
    } else {
      router.push('/login');
    }
  }, [router]);

  return (
    <div className="flex justify-center items-center h-64">
      <p className="text-gray-500">Chargement de l'application...</p>
    </div>
  );
}