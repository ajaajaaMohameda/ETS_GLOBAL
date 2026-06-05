'use client';

import { useState, useEffect } from 'react';
import { userService } from '@/services/user.service';

export default function ProfilePage() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const user = await userService.getCurrentUser();
        setName(user.name);
        setEmail(user.email);
      } catch (err: any) {
        setError('Erreur lors du chargement des données utilisateur.');
      } finally {
        setIsLoading(false);
      }
    };

    fetchUser();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    setIsSaving(true);

    try {
      await userService.updateProfile(name, email);
      setSuccess('Vos informations ont été mises à jour avec succès.');
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erreur lors de la mise à jour du profil.');
    } finally {
      setIsSaving(false);
    }
  };

  if (isLoading) {
    return <div className="flex justify-center items-center h-64">Chargement de votre profil...</div>;
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="md:flex md:items-center md:justify-between">
        <div className="min-w-0 flex-1">
          <h2 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Mon Profil
          </h2>
        </div>
      </div>

      <div className="bg-white shadow-sm sm:rounded-lg border border-gray-200">
        <div className="px-4 py-5 sm:p-6">
          <h3 className="text-base font-semibold leading-6 text-gray-900">
            Informations personnelles
          </h3>
          <div className="mt-2 max-w-xl text-sm text-gray-500">
            <p>Mettez à jour votre nom et votre adresse email liés à votre compte.</p>
          </div>
          
          {error && (
            <div className="mt-4 bg-red-50 text-red-500 p-4 rounded-md text-sm font-medium">
              {error}
            </div>
          )}
          
          {success && (
            <div className="mt-4 bg-green-50 text-green-600 p-4 rounded-md text-sm font-medium">
              {success}
            </div>
          )}

          <form className="mt-5 sm:flex sm:items-center" onSubmit={handleSubmit}>
            <div className="w-full sm:max-w-xs space-y-4">
              <div>
                <label htmlFor="name" className="block text-sm font-medium leading-6 text-gray-900">
                  Nom complet
                </label>
                <input
                  type="text"
                  name="name"
                  id="name"
                  required
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="mt-2 block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium leading-6 text-gray-900">
                  Adresse email
                </label>
                <input
                  type="email"
                  name="email"
                  id="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="mt-2 block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                />
              </div>
              <button
                type="submit"
                disabled={isSaving}
                className="mt-3 inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:mt-4 sm:w-auto disabled:opacity-50 transition-colors"
              >
                {isSaving ? 'Enregistrement...' : 'Enregistrer'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}