import { useState } from 'react';
import type { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';

type AccountType = 'customer' | 'individual' | 'organization';
type OrganizationType = 'private' | 'government' | 'multinational' | 'ngo' | 'other';

type RegistrationData = {
    name: string;
    email: string;
    phone: string;
    account_type: AccountType;
    organization_name: string;
    organization_type: OrganizationType;
    password: string;
    password_confirmation: string;
};

export default function Register() {
    const [accountType, setAccountType] = useState<AccountType>('customer');
    const { data, setData, post, processing, errors } = useForm<RegistrationData>({
        name: '',
        email: '',
        phone: '',
        account_type: 'customer',
        organization_name: '',
        organization_type: 'private',
        password: '',
        password_confirmation: '',
    });

    const chooseAccount = (value: AccountType) => {
        setAccountType(value);
        setData('account_type', value);
    };

    const submit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        post('/register');
    };

    return (
        <>
            <Head title="Create account" />
            <main className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10">
                <section className="w-full max-w-xl rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 sm:p-8">
                    <div className="mb-7">
                        <h1 className="text-2xl font-bold text-gray-900">Create your account</h1>
                        <p className="mt-2 text-sm text-gray-600">Join the Bangladesh online directory.</p>
                    </div>

                    <form onSubmit={submit} className="space-y-5">
                        <fieldset>
                            <legend className="mb-2 block text-sm font-medium text-gray-700">Account type</legend>
                            <div className="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                {([
                                    ['customer', 'Customer'],
                                    ['individual', 'Individual expert'],
                                    ['organization', 'Organization'],
                                ] as [AccountType, string][]).map(([value, label]) => (
                                    <button
                                        type="button"
                                        key={value}
                                        onClick={() => chooseAccount(value)}
                                        className={`rounded-lg border px-3 py-3 text-sm font-medium ${accountType === value ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'}`}
                                    >
                                        {label}
                                    </button>
                                ))}
                            </div>
                            {errors.account_type && <p className="mt-1 text-sm text-red-600">{errors.account_type}</p>}
                        </fieldset>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="name" className="mb-1 block text-sm font-medium text-gray-700">Your name</label>
                                <input id="name" value={data.name} onChange={e => setData('name', e.target.value)} autoComplete="name" required className="w-full rounded-lg border-gray-300" />
                                {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                            </div>
                            <div>
                                <label htmlFor="phone" className="mb-1 block text-sm font-medium text-gray-700">Mobile number</label>
                                <input id="phone" value={data.phone} onChange={e => setData('phone', e.target.value)} autoComplete="tel" className="w-full rounded-lg border-gray-300" />
                                {errors.phone && <p className="mt-1 text-sm text-red-600">{errors.phone}</p>}
                            </div>
                        </div>

                        <div>
                            <label htmlFor="email" className="mb-1 block text-sm font-medium text-gray-700">Email address</label>
                            <input id="email" type="email" value={data.email} onChange={e => setData('email', e.target.value)} autoComplete="email" required className="w-full rounded-lg border-gray-300" />
                            {errors.email && <p className="mt-1 text-sm text-red-600">{errors.email}</p>}
                        </div>

                        {accountType === 'organization' && (
                            <div className="space-y-4 rounded-lg bg-gray-50 p-4">
                                <h2 className="font-semibold text-gray-900">Organization information</h2>
                                <div>
                                    <label htmlFor="organization_name" className="mb-1 block text-sm font-medium text-gray-700">Organization name</label>
                                    <input id="organization_name" value={data.organization_name} onChange={e => setData('organization_name', e.target.value)} required className="w-full rounded-lg border-gray-300" />
                                    {errors.organization_name && <p className="mt-1 text-sm text-red-600">{errors.organization_name}</p>}
                                </div>
                                <div>
                                    <label htmlFor="organization_type" className="mb-1 block text-sm font-medium text-gray-700">Organization type</label>
                                    <select id="organization_type" value={data.organization_type} onChange={e => setData('organization_type', e.target.value as OrganizationType)} className="w-full rounded-lg border-gray-300">
                                        <option value="private">Private organization</option>
                                        <option value="government">Government organization</option>
                                        <option value="multinational">Multinational organization</option>
                                        <option value="ngo">NGO / Development organization</option>
                                        <option value="other">Other</option>
                                    </select>
                                    {errors.organization_type && <p className="mt-1 text-sm text-red-600">{errors.organization_type}</p>}
                                </div>
                            </div>
                        )}

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="password" className="mb-1 block text-sm font-medium text-gray-700">Password</label>
                                <input id="password" type="password" value={data.password} onChange={e => setData('password', e.target.value)} autoComplete="new-password" required className="w-full rounded-lg border-gray-300" />
                                {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                            </div>
                            <div>
                                <label htmlFor="password_confirmation" className="mb-1 block text-sm font-medium text-gray-700">Confirm password</label>
                                <input id="password_confirmation" type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} autoComplete="new-password" required className="w-full rounded-lg border-gray-300" />
                            </div>
                        </div>

                        <button type="submit" disabled={processing} className="w-full rounded-lg bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60">
                            {processing ? 'Creating account...' : 'Create account'}
                        </button>
                    </form>

                    <p className="mt-6 text-center text-sm text-gray-600">
                        Already registered? <Link href="/login" className="font-semibold text-blue-700 hover:underline">Log in</Link>
                    </p>
                </section>
            </main>
        </>
    );
}
