import { SVGAttributes } from 'react';

export default function ApplicationLogo(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
            focusable="false"
        >
            <rect
                x="3"
                y="3"
                width="42"
                height="42"
                rx="13"
                fill="currentColor"
                opacity="0.12"
            />
            <path
                d="M15 9h18a4 4 0 0 1 4 4v22a4 4 0 0 1-4 4H15a4 4 0 0 1-4-4V13a4 4 0 0 1 4-4Z"
                fill="currentColor"
                opacity="0.2"
            />
            <path
                d="m17 17 14 14M31 17 17 31"
                stroke="currentColor"
                strokeWidth="3.5"
                strokeLinecap="round"
            />
            <path
                d="M16 37h16"
                stroke="currentColor"
                strokeWidth="2.5"
                strokeLinecap="round"
                opacity="0.55"
            />
        </svg>
    );
}
