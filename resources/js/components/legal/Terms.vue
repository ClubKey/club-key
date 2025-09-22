<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog';
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <slot>
                <Button variant="link">Terms</Button>
            </slot>
        </DialogTrigger>
        <DialogContent class="max-h-[80vh] overflow-y-auto sm:max-w-screen-lg">
            <div class="prose prose-lg grid max-h-full gap-4 py-4 dark:prose-invert">
                <section>
                    <h1>Terms of Service</h1>
                    <h2>Welcome</h2>
                    <p>
                        Welcome to <a href="https://clubkey.site">https://clubkey.site (ClubKey/ Club Key)</a>. The <em>Site</em> is comprised of
                        various web pages operated by Club Key. The Site is offered to you conditioned on your acceptance, without modification, of
                        the terms, conditions, and notices contained herein (the "Terms"). Your use of the Site constitutes your agreement to all such
                        Terms. Please read these Terms carefully and keep a copy for your reference.
                    </p>
                    <p>
                        <strong>What we are:</strong> Club Key is retail loyalty software — a loyalty club program that rewards clients based on
                        visits. Discounts are associated with different tier levels.
                    </p>
                </section>

                <section>
                    <h2>Electronic Communications</h2>
                    <p>
                        Visiting <a href="https://clubkey.site">https://clubkey.site</a> or sending emails to Club Key constitutes electronic
                        communications. You consent to receive electronic communications and agree that all agreements, notices, disclosures, and
                        other communications we provide to you electronically (via email and on the Site) satisfy any legal requirement that such
                        communications be in writing.
                    </p>
                </section>

                <section>
                    <h2>Your Account</h2>
                    <p>
                        If you use this Site, you are responsible for maintaining the confidentiality of your account and password and for restricting
                        access to your computer. You accept responsibility for all activities that occur under your account or password. You may not
                        assign or otherwise transfer your account to any other person or entity.
                    </p>
                    <p>
                        You acknowledge that Club Key is not responsible for third-party access to your account that results from theft or
                        misappropriation of your account. Club Key and its associates reserve the right to refuse or cancel service, terminate
                        accounts, or remove or edit content in their sole discretion.
                    </p>
                </section>

                <section>
                    <h2>Children Under Thirteen</h2>
                    <p>
                        Club Key does not knowingly collect, either online or offline, personal information from persons under the age of thirteen. If
                        you are under 18, you may use the Site only with permission of a parent or guardian.
                    </p>
                </section>

                <section>
                    <h2>Links to Third Party Sites / Third Party Services</h2>
                    <p>
                        The Site may contain links to other websites ("Linked Sites"). Linked Sites are not under Club Key's control and Club Key is
                        not responsible for their contents, links, or updates. Links are provided for your convenience and inclusion does not imply
                        endorsement.
                    </p>
                    <p>
                        Certain services available through the Site are delivered by third parties. By using any product, service, or functionality
                        originating from the Site, you acknowledge and consent that Club Key may share information and data with third parties with
                        whom Club Key has a contractual relationship to provide the requested product, service, or functionality.
                    </p>
                </section>

                <section>
                    <h2>No Unlawful or Prohibited Use / Intellectual Property</h2>
                    <p>
                        You are granted a non-exclusive, non-transferable, revocable license to access and use the Site strictly in accordance with
                        these Terms. As a condition of use, you warrant you will not use the Site for any unlawful or prohibited purpose.
                    </p>
                    <ul>
                        <li>You may not damage, disable, overburden, or impair the Site.</li>
                        <li>You may not interfere with any other party's use of the Site.</li>
                        <li>You may not obtain materials or information through means not intentionally provided by the Site.</li>
                    </ul>
                    <p>
                        All content included as part of the Service — text, graphics, logos, images, and software — is the property of Club Key or its
                        suppliers and is protected by copyright and other intellectual property laws. You agree to observe all proprietary notices and
                        will not modify, publish, transmit, reverse engineer, transfer, sell, create derivative works of, or otherwise exploit any
                        content from the Site except as expressly permitted by Club Key in writing.
                    </p>
                    <p>
                        Your use of the Site does not grant you any ownership rights in content and Club Key grants no licenses to its intellectual
                        property beyond what is expressly stated in these Terms.
                    </p>
                </section>

                <section>
                    <h2>Third Party Accounts</h2>
                    <p>
                        You may be able to connect your Club Key account to third party accounts. By connecting, you consent to the continuous release
                        of information about you to others in accordance with your privacy settings on those third party sites. If you do not want
                        information shared in this manner, do not use this feature.
                    </p>
                </section>

                <section>
                    <h2>International Users</h2>
                    <p>
                        The Service is controlled, operated, and administered by Club Key from within the United States. If you access the Service
                        from outside the U.S., you are responsible for compliance with local laws. You agree not to use the Site in any country or
                        manner prohibited by applicable laws, restrictions, or regulations.
                    </p>
                </section>

                <section>
                    <h2>Indemnification</h2>
                    <p>
                        You agree to indemnify, defend, and hold harmless Club Key, its officers, directors, employees, agents, and third parties for
                        any losses, costs, liabilities, and expenses (including reasonable attorney's fees) arising out of:
                    </p>
                    <ul>
                        <li>Your use of or inability to use the Site or services;</li>
                        <li>Any user postings made by you;</li>
                        <li>Your violation of these Terms;</li>
                        <li>Your violation of any rights of a third party; or</li>
                        <li>Your violation of applicable laws, rules, or regulations.</li>
                    </ul>
                    <p>
                        Club Key reserves the right, at its own cost, to assume exclusive defense and control of any matter otherwise subject to
                        indemnification by you, and you will cooperate with Club Key's defense.
                    </p>
                </section>

                <section>
                    <h2>Arbitration</h2>
                    <p>
                        If the parties cannot resolve any dispute arising out of these Terms, the dispute shall be resolved only by final and binding
                        arbitration pursuant to the Federal Arbitration Act. Arbitration will be conducted by a single neutral arbitrator and
                        administered by the American Arbitration Association or a similar arbitration service selected by the parties, in a mutually
                        agreed location.
                    </p>
                    <p>
                        The arbitrator's award is final and judgment may be entered in any court having jurisdiction. The prevailing party in any
                        action, proceeding, or arbitration arising out of these Terms will be entitled to recover costs and reasonable attorney's
                        fees.
                    </p>
                    <p>
                        The parties agree to arbitrate all disputes and claims related to these Terms, including tort claims arising from them. The
                        Federal Arbitration Act governs this provision. The arbitrator determines the scope and enforceability of this arbitration
                        provision. This arbitration provision survives termination of these Terms.
                    </p>
                </section>

                <section>
                    <h2>Class Action Waiver</h2>
                    <p>
                        Any arbitration under these Terms will proceed on an individual basis; class arbitrations and class/representative/collective
                        actions are not permitted. Parties may bring claims only in their individual capacity, not as a plaintiff or class member in
                        any putative class, collective, or representative proceeding. Unless both you and Club Key agree otherwise, the arbitrator may
                        not consolidate more than one person's claims or preside over any form of representative or class proceeding.
                    </p>
                </section>

                <section>
                    <h2>Liability Disclaimer</h2>
                    <p>
                        The information, software, products, and services available through the Site may include inaccuracies or typographical errors.
                        Changes are periodically added. Club Key and its suppliers may make improvements or changes at any time.
                    </p>
                    <p>
                        TO THE MAXIMUM EXTENT PERMITTED BY LAW, ALL SUCH INFORMATION, SOFTWARE, PRODUCTS, SERVICES, AND GRAPHICS ARE PROVIDED "AS IS"
                        WITHOUT WARRANTY OR CONDITION OF ANY KIND. CLUB KEY AND ITS SUPPLIERS DISCLAIM ALL WARRANTIES, INCLUDING IMPLIED WARRANTIES OF
                        MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE, AND NON-INFRINGEMENT.
                    </p>
                    <p>
                        TO THE MAXIMUM EXTENT PERMITTED BY LAW, IN NO EVENT SHALL CLUB KEY OR ITS SUPPLIERS BE LIABLE FOR ANY DIRECT, INDIRECT,
                        PUNITIVE, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR OTHER DAMAGES (INCLUDING LOSS OF USE, DATA, OR PROFITS) ARISING OUT OF OR IN
                        CONNECTION WITH THE SITE, SERVICES, OR RELATED GRAPHICS, EVEN IF ADVISED OF THE POSSIBILITY OF DAMAGES.
                    </p>
                    <p>
                        Because some jurisdictions do not allow exclusion or limitation of certain damages, the above limitations may not apply to
                        you. If you are dissatisfied with any portion of the Site or these Terms, your sole remedy is to discontinue use of the Site.
                    </p>
                </section>

                <section>
                    <h2>Termination / Access Restriction</h2>
                    <p>
                        Club Key reserves the right, in its sole discretion, to terminate your access to the Site and related services or any portion
                        thereof at any time, without notice.
                    </p>
                    <p>
                        To the maximum extent permitted by law, this agreement is governed by the laws of the State of Washington and you consent to
                        the exclusive jurisdiction and venue of courts in Washington for disputes relating to use of the Site. Use of the Site is
                        unauthorized in any jurisdiction that does not give effect to all provisions of these Terms.
                    </p>
                    <p>
                        You agree that no joint venture, partnership, employment, or agency relationship exists between you and Club Key as a result
                        of this agreement or use of the Site.
                    </p>
                    <p>
                        Club Key's performance of this agreement is subject to existing laws and legal process; nothing in this agreement limits Club
                        Key's right to comply with government, court, or law enforcement requests concerning your use of the Site or information
                        gathered about such use.
                    </p>
                    <p>
                        If any part of this agreement is determined invalid or unenforceable, that provision will be replaced by a valid provision
                        that most closely matches the original intent and the remainder of the agreement will continue in effect.
                    </p>
                    <p>
                        This agreement constitutes the entire agreement between you and Club Key with respect to the Site and supersedes all prior
                        communications and proposals, whether electronic, oral, or written.
                    </p>
                    <p>
                        A printed version of this agreement and any electronic notices will be admissible in judicial or administrative proceedings to
                        the same extent as other business documents originally generated in printed form. It is the express wish of the parties that
                        this agreement and related documents be written in English.
                    </p>
                </section>

                <section>
                    <h2>Changes to Terms</h2>
                    <p>
                        Club Key reserves the right, in its sole discretion, to change the Terms under which the Site is offered. The most current
                        version of the Terms will supersede previous versions. Club Key encourages you to periodically review the Terms to stay
                        informed of updates.
                    </p>
                </section>

                <section>
                    <h2>Contact Us</h2>
                    <address>
                        <p>Club Key</p>
                        <p>9425 Sandifur Parkway<br />Pasco, Washington 99301</p>
                        <p>Email: <a href="mailto:crobbins@ahavacoffeeco.com">crobbins@ahavacoffeeco.com</a></p>
                    </address>
                </section>

                <footer>
                    <p>© Club Key. Effective date: September 16, 2025.</p>
                </footer>
            </div>
            <DialogFooter>
                <DialogClose as-child>
                    <Button type="button">Acknowledge</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
